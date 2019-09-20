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

use ReflectionProperty;
use Chevere\Contracts\VarDump\FormatterContract;
use Chevere\VarDump\Processors\ArrayProcessor;
use Chevere\VarDump\Processors\BooleanProcessor;
use Chevere\VarDump\Processors\ObjectProcessor;
use Chevere\VarDump\Processors\ScalarProcessor;

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

    public function formatter(): FormatterContract
    {
        return $this->formatter;
    }

    public function dontDump(): array
    {
        return $this->dontDump;
    }

    public function setDontDump(array $dontDump): void
    {
        $this->dontDump = $dontDump;
    }

    public function dump($var, int $indent = 0, int $depth = 0): void
    {
        ++$depth;
        $this->var = $var;
        // if (is_array($this->var)) {
        //     $this->expression = [] + $this->var;
        // } else {
        // }
        $this->expression = $this->var;
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

    public function expression()
    {
        return $this->expression;
    }

    public function indent(): int
    {
        return $this->indent;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function indentString(): string
    {
        return $this->indentString;
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
        $template = $this->template;
        if (!empty($this->parentheses)) {
            $parentheses = $this->formatter->wrap(static::_OPERATOR, '(' . $this->parentheses . ')');
        } else {
            $parentheses = null;
            $template = str_replace('%parentheses%', null, $template);
            $template = preg_replace('!\s+!', ' ', $template);
            $template = trim($template);
        }
        $this->output = strtr($template, [
            '%type%' => $this->formatter->wrap($this->type, $this->type),
            '%val%' => $this->val,
            '%parentheses%' => $parentheses,
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
                $processor = new BooleanProcessor($this->expression, $this);
                break;
            case static::TYPE_ARRAY:
                ++$this->indent;
                $processor = new ArrayProcessor($this->expression, $this);
                break;
            case static::TYPE_OBJECT:
                ++$this->indent;
                $processor = new ObjectProcessor($this->expression, $this);
                break;
            default:
                $processor = new ScalarProcessor($this->expression, $this);
                break;
        }
        $this->val .= $processor->val();
        $this->parentheses = $processor->parentheses();
    }

    private function setTemplate(): void
    {
        switch ($this->type) {
            case static::TYPE_ARRAY:
            case static::TYPE_OBJECT:
                $this->template = '%type% %parentheses% %val%';
                break;
            default:
                $this->template = '%type% %val% %parentheses%';
                break;
        }
    }

    private function handleParentheses(): void
    {
        if (!empty($this->parentheses) && false !== strpos($this->parentheses, '=')) {
            $this->parentheses = $this->formatter->getEmphasis($this->parentheses);
        }
    }
}
