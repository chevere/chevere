<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere\Utils;

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Chevereto\Chevere\Path;
use Reflector;
use ReflectionObject;
use ReflectionProperty;

/**
 * Another var_dump replacement.
 * FIXME: Doesn't work with BetterReflection objects.
 */
class Dump
{
    const TYPE_STRING = 'string';
    const TYPE_FLOAT = 'float';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_NULL = 'NULL';
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY = 'array';
    const _FILE = '_file';
    const _CLASS = '_class';
    const _OPERATOR = '_operator';
    const _FUNCTION = '_function';
    const ANON_CLASS = 'class@anonymous';

    /**
     * Default color palette for each type and thing used.
     * [id => color].
     */
    const PALETTE = [
        self::TYPE_STRING => '#e67e22', // orange
        self::TYPE_FLOAT => '#f1c40f', // yellow
        self::TYPE_INTEGER => '#f1c40f', // yellow
        self::TYPE_BOOLEAN => '#9b59b6', // purple
        self::TYPE_NULL => '#7f8c8d', // grey
        self::TYPE_OBJECT => '#e74c3c', // red
        self::TYPE_ARRAY => '#2ecc71', // green
        self::_FILE => null,
        self::_CLASS => '#3498db', // blue
        self::_OPERATOR => '#7f8c8d', // grey
        self::_FUNCTION => '#9b59b6', // purple
    ];

    /**
     * Default color palette for each type and thing used.
     * [id => color_code].
     */
    const CONSOLE_PALETTE = [
        self::TYPE_STRING => 'color_136', // yellow
        self::TYPE_FLOAT => 'color_136', // yellow
        self::TYPE_INTEGER => 'color_136', // yellow
        self::TYPE_BOOLEAN => 'color_127', // purple
        self::TYPE_NULL => 'color_245', // grey
        self::TYPE_OBJECT => 'color_167', // red
        self::TYPE_ARRAY => 'color_41', // green
        self::_FILE => null,
        self::_CLASS => 'color_147', // blue
        self::_OPERATOR => 'color_245', // grey
        self::_FUNCTION => 'color_127', // purple
    ];

    const PROPERTIES_REFLECTION_MAP = [
        'public' => ReflectionProperty::IS_PUBLIC,
        'protected' => ReflectionProperty::IS_PROTECTED,
        'private' => ReflectionProperty::IS_PRIVATE,
        'static' => ReflectionProperty::IS_STATIC,
    ];

    /** @var string */
    protected $output;

    /** @var string */
    protected $template;

    /** @var string */
    protected $parentheses;

    /** @var string */
    protected $type;

    /** @var mixed */
    protected $val;

    /** @var string */
    protected $prefix;

    /** @var string */
    protected $expression;

    /** @var string */
    protected $className;

    /** @var mixed */
    private $var;

    /** @var int */
    private $indent;

    /** @var array */
    private $dontDump;

    /** @var int */
    private $depth;

    /** @var array */
    private $properties;

    /** @var Reflector */
    private $reflectionObject;

    public function __construct($var, int $indent = null, array $dontDump = [], int $depth = 0)
    {
        ++$depth;
        $this->var = $var;
        $this->indent = $indent ?? 0;
        $this->dontDump = $dontDump;
        $this->depth = $depth;
        // Maybe improve this to support any circular reference?
        if (is_array($this->var)) {
            $this->expression = array_merge([], $this->var);
        } else {
            $this->expression = $this->var;
        }
        $this->val = null;
        $this->prefix = str_repeat(' <span style="border-left: 1px solid #bdc3c7;"></span>  ', $this->indent);
        $this->type = gettype($this->expression);
        if ('double' == $this->type) {
            $this->type = static::TYPE_FLOAT;
        }
        $this->handleTypes();
        $this->handleSetTemplate();
        $this->handleSetParentheses();
        $this->setOutput(strtr($this->template, [
            '%type' => static::wrap($this->type, $this->type),
            '%val' => $this->val,
            '%parentheses' => isset($this->parentheses) ? static::wrap(static::_OPERATOR, '('.$this->parentheses.')') : null,
        ]));
    }

    protected function handleTypes(): void
    {
        switch ($this->type) {
            case static::TYPE_BOOLEAN:
                $this->appendVal($this->expression ? 'TRUE' : 'FALSE');
            break;
            case static::TYPE_ARRAY:
                $this->handleArrayType();
            break;
            case static::TYPE_OBJECT:
                $this->handleObjectType();
            break;
            default:
                $this->handleDefaultType();
            break;
        }
    }

    protected function handleObjectType(): void
    {
        ++$this->indent;
        $this->reflectionObject = new ReflectionObject($this->expression);
        if (in_array($this->reflectionObject->getName(), $this->dontDump)) {
            $this->appendVal(static::wrap(static::_OPERATOR, '<i>'.$this->reflectionObject->getName().'</i>'));

            return;
        }
        $this->handleSetObjectProperties();
        $this->processObjectProperties();
        $this->setClassName(get_class($this->expression));
        $this->handleNormalizeClassName();
        $this->setParentheses($this->className);
    }

    protected function handleSetObjectProperties(): void
    {
        $this->properties = [];
        foreach (static::PROPERTIES_REFLECTION_MAP as $k => &$v) {
            $v = $this->reflectionObject->getProperties($v);
            foreach ($v as $kk => $vv) {
                if (!isset($this->properties[$vv->getName()])) {
                    $vv->setAccessible(true);
                    $this->properties[$vv->getName()] = ['value' => $vv->getValue($this->expression)];
                }
                $this->properties[$vv->getName()]['visibility'][] = $k;
            }
        }
    }

    protected function processObjectProperties(): void
    {
        foreach ($this->properties as $k => $v) {
            $isCircularRef = false;
            $visibility = implode(' ', $this->properties[$k]['visibility'] ?? $this->properties['visibility']);
            $operator = static::wrap(static::_OPERATOR, '->');
            $this->appendVal("\n$this->prefix <i>$visibility</i> ".htmlspecialchars($k)." $operator ");
            $aux = $v['value'];
            if (is_object($aux) && property_exists($aux, $k)) {
                try {
                    $r = new ReflectionObject($aux);
                    $p = $r->getProperty($k);
                    $p->setAccessible(true);

                    if ($aux == $p->getValue($aux)) {
                        $isCircularRef = true;
                    }
                } catch (Throwable $e) {
                    continue;
                }
            }
            if ($isCircularRef) {
                $this->appendVal(static::wrap(static::_OPERATOR, '(<i>circular object reference</i>)'));
            } else {
                if ($this->depth < 4) {
                    $this->appendVal(new static($aux, $this->indent, $this->dontDump, $this->depth));
                } else {
                    $this->appendVal(static::wrap(static::_OPERATOR, '(<i>max depth reached</i>)'));
                }
            }
        }
    }

    protected function handleArrayType(): void
    {
        ++$this->indent;
        foreach ($this->expression as $k => $v) {
            $operator = static::wrap(static::_OPERATOR, '=>');
            $this->appendVal("\n$this->prefix ".htmlspecialchars((string) $k)." $operator ");
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $this->appendVal(static::wrap(static::_OPERATOR, '(<i>circular array reference</i>)'));
            } else {
                $this->appendVal((string) new static($aux, $this->indent, $this->dontDump));
            }
        }
        $this->setParentheses('size='.count($this->expression));
    }

    protected function handleNormalizeClassName(): void
    {
        if (Str::startsWith(static::ANON_CLASS, $this->className)) {
            $this->setClassName(Path::normalize($this->className));
        }
    }

    protected function handleDefaultType(): void
    {
        $is_string = is_string($this->expression);
        $is_numeric = is_numeric($this->expression);
        if ($is_string || $is_numeric) {
            $this->setParentheses('length='.strlen($is_numeric ? ((string) $this->expression) : $this->expression));
            $this->appendVal(strval($this->expression)); // htmlspecialchars($this->expression)
        }
    }

    protected function handleSetTemplate(): void
    {
        switch ($this->type) {
            case static::TYPE_ARRAY:
            case static::TYPE_OBJECT:
                $this->setTemplate('%type %parentheses%val');
            break;
            default:
                $this->setTemplate('%type %val %parentheses');
            break;
        }
    }

    protected function handleSetParentheses(): void
    {
        if (isset($this->parentheses) && false !== strpos($this->parentheses, '=')) {
            $this->setParentheses('<i>'.$this->parentheses.'</i>');
        }
    }

    protected function setClassName(string $className): void
    {
        $this->className = $className;
    }

    protected function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    protected function appendVal($val): void
    {
        $this->val .= $val;
    }

    protected function setParentheses(string $parentheses): void
    {
        $this->parentheses = $parentheses;
    }

    protected function setOutput(string $output): void
    {
        $this->output = $output;
    }

    public function __toString(): string
    {
        return $this->output;
    }

    /**
     * Dumps information about a variable.
     *
     * @param mixed $var      anything
     * @param int   $indent   left padding (spaces) for this entry
     * @param array $dontDump array containing classes that won't get dumped
     *
     * @return string parsed dump string
     */
    public static function out($var, int $indent = null, array $dontDump = [], int $depth = 0): string
    {
        return (string) new static(...func_get_args());
    }

    /**
     * Get color for palette key.
     *
     * @param string $key color palette key
     *
     * @return string color
     */
    public static function getColorForKey(string $key): ?string
    {
        return 'cli' == php_sapi_name() ? static::CONSOLE_PALETTE[$key] ?? null : static::PALETTE[$key] ?? null;
    }

    /**
     * Wrap dump data into HTML.
     *
     * @param string $key  Type or algo key (see constants)
     * @param mixed  $dump dump data
     *
     * @return string wrapped dump data
     */
    public static function wrap(string $key, $dump): ?string
    {
        $color = static::getColorForKey($key);
        if (isset($color)) {
            if ('cli' == php_sapi_name()) {
                $consoleColor = new ConsoleColor();

                return $consoleColor->apply($color, $dump);
            }

            return '<span style="color:'.$color.'">'.$dump.'</span>';
        } else {
            return (string) $dump;
        }
    }
}
