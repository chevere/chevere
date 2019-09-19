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

namespace Chevere\VarDump;

use Chevere\Contracts\VarDump\FormatterContract;
use Throwable;
use Reflector;
use ReflectionProperty;
use ReflectionObject;
use Chevere\Path\Path;
use Chevere\Str\Str;

/**
 * Analyze a variable and provide a formated string representation of its type and data.
 */
final class VarDump
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

    const PROPERTIES_REFLECTION_MAP = [
        'public' => ReflectionProperty::IS_PUBLIC,
        'protected' => ReflectionProperty::IS_PROTECTED,
        'private' => ReflectionProperty::IS_PRIVATE,
        'static' => ReflectionProperty::IS_STATIC,
    ];

    /** @var FormatterContract */
    private $formatter;

    /** @var array [className,] */
    private $dontDump;

    /** @var string */
    private $output;

    /** @var string */
    private $template;

    /** @var string */
    private $className;

    /** @var array */
    private $properties;

    /** @var Reflector */
    private $reflectionObject;

    private $var;

    /** @var mixed */
    private $expression;

    /** @var int */
    private $indent;

    /** @var int */
    private $depth;

    /** @var mixed */
    private $val;

    /** @var string */
    private $indentString;

    /** @var string */
    private $type;

    /** @var string */
    private $parentheses;

    public function __construct(FormatterContract $formatter)
    {
        $this->formatter = $formatter;
        $this->dontDump = [];
    }

    public function setDontDump(array $dontDump): void
    {
        $this->dontDump = $dontDump;
    }

    public function dump($var, int $indent = 0, int $depth = 0): void
    {
        ++$depth;
        $this->var = $var;
        if (is_array($this->var)) {
            $this->expression = [] + $this->var;
        } else {
            $this->expression = $this->var;
        }
        $this->indent = $indent;
        $this->depth = $depth;
        $this->val = null;
        $this->indentString = $this->formatter->getIndent($this->indent);
        $this->setType();
        $this->handleType();
        $this->setTemplate();
        $this->handleParentheses();
        $this->setOutput();
    }

    public function toString(): string
    {
        return $this->output ?? '';
    }

    public function respawn(): self
    {
        $new = new self($this->formatter);
        if (!empty($this->dontDump)) {
            $new->setDontDump($this->dontDump);
        }
        return $new;
    }

    private function setOutput(): void
    {
        $this->output = strtr($this->template, [
            '%type' => $this->formatter->wrap($this->type, $this->type),
            '%val' => $this->val,
            '%parentheses' => isset($this->parentheses)
                ? $this->formatter->wrap(static::_OPERATOR, '(' . $this->parentheses . ')')
                : null,
        ]);
    }

    private function setType(): void
    {
        $this->type = gettype($this->expression);
        if ('double' == $this->type) {
            $this->type = static::TYPE_FLOAT;
        }
    }

    private function handleType(): void
    {
        switch ($this->type) {
            case static::TYPE_BOOLEAN:
                $this->val .= $this->expression ? 'TRUE' : 'FALSE';
                break;
            case static::TYPE_ARRAY:
                ++$this->indent;
                $this->processArray();
                break;
            case static::TYPE_OBJECT:
                ++$this->indent;
                $this->processObject();
                break;
            default:
                $this->processDefault();
                break;
        }
    }

    private function processObject(): void
    {
        $this->reflectionObject = new ReflectionObject($this->expression);
        if (in_array($this->reflectionObject->getName(), $this->dontDump)) {
            $this->val .= $this->formatter->wrap(
                static::_OPERATOR,
                $this->formatter->getEmphasis(
                    $this->reflectionObject->getName()
                )
            );

            return;
        }
        $this->setProperties();
        foreach ($this->properties as $k => $v) {
            $this->processObjectProperty($k, $v);
        }
        $this->className = get_class($this->expression);
        $this->handleNormalizeClassName();
        $this->parentheses = $this->className;
    }

    private function setProperties(): void
    {
        $this->properties = [];
        foreach (static::PROPERTIES_REFLECTION_MAP as $visibility => $filter) {
            /** @scrutinizer ignore-call */
            $properties = $this->reflectionObject->getProperties($filter);
            foreach ($properties as $property) {
                if (!isset($this->properties[$property->getName()])) {
                    $property->setAccessible(true);
                    // var_dump($this->reflectionObject->getProperty($property->getName()));
                    try {
                        $value = $property->getValue($this->expression);
                    } catch (Throwable $e) {
                        // $value = '';
                    }

                    $this->properties[$property->getName()] = ['value' => $value];
                }
                $this->properties[$property->getName()]['visibility'][] = $visibility;
            }
        }
    }

    private function processObjectProperty($key, $var): void
    {
        $visibility = implode(' ', $var['visibility'] ?? $this->properties['visibility']);
        $operator = $this->formatter->wrap(static::_OPERATOR, '->');
        $this->val .= "\n" . $this->indentString . $this->formatter->getEmphasis($visibility) . ' ' . $this->formatter->getEncodedChars($key) . " $operator ";
        $aux = $var['value'];
        if (is_object($aux) && property_exists($aux, $key)) {
            try {
                $r = new ReflectionObject($aux);
                $p = $r->getProperty($key);
                $p->setAccessible(true);
                if ($aux == $p->getValue($aux)) {
                    $this->val .= $this->formatter->wrap(
                        static::_OPERATOR,
                        '(' . $this->formatter->getEmphasis('circular object reference') . ')'
                    );
                }
                return;
            } catch (Throwable $e) {
                return;
            }
        }
        if ($this->depth < 4) {
            $new = $this->respawn();
            $new->dump($aux, $this->indent, $this->depth);
            $this->val .= $new->toString();
        } else {
            $this->val .= $this->formatter->wrap(
                static::_OPERATOR,
                '(' . $this->formatter->getEmphasis('max depth reached') . ')'
            );
        }
    }

    private function processArray(): void
    {
        foreach ($this->expression as $k => $v) {
            $operator = $this->formatter->wrap(static::_OPERATOR, '=>');
            $this->val .= "\n" . $this->indentString . ' ' . $this->formatter->getEncodedChars((string) $k) . " $operator ";
            $aux = $v;
            $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
            if ($isCircularRef) {
                $this->val .= $this->formatter->wrap(
                    static::_OPERATOR,
                    '(' . $this->formatter->getEmphasis('circular array reference') . ')'
                );
            } else {
                $new = $this->respawn();
                $new->dump($aux, $this->indent);
                $this->val .= $new->toString();
            }
        }
        $this->parentheses = 'size=' . count($this->expression);
    }

    private function handleNormalizeClassName(): void
    {
        if (Str::startsWith(static::ANON_CLASS, $this->className)) {
            $this->className = Path::normalize($this->className);
        }
    }

    private function processDefault(): void
    {
        $is_string = is_string($this->expression);
        $is_numeric = is_numeric($this->expression);
        if ($is_string || $is_numeric) {
            $this->parentheses = 'length=' . strlen($is_numeric ? ((string) $this->expression) : $this->expression);
            $this->val .= $this->formatter->getEncodedChars(strval($this->expression));
        }
    }

    private function setTemplate(): void
    {
        switch ($this->type) {
            case static::TYPE_ARRAY:
            case static::TYPE_OBJECT:
                $this->template = '%type %parentheses%val';
                break;
            default:
                $this->template = '%type %val %parentheses';
                break;
        }
    }

    private function handleParentheses(): void
    {
        if (isset($this->parentheses) && false !== strpos($this->parentheses, '=')) {
            $this->parentheses = $this->formatter->getEmphasis($this->parentheses);
        }
    }
}
