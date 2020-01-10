<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump;

use Chevere\Components\VarDump\Processors\ArrayProcessor;
use Chevere\Components\VarDump\Processors\BooleanProcessor;
use Chevere\Components\VarDump\Processors\ObjectProcessor;
use Chevere\Components\VarDump\Processors\ScalarProcessor;
use Chevere\Components\VarDump\Contracts\FormatterContract;
use Chevere\Components\VarDump\Contracts\VarDumpContract;
use function ChevereFn\varType;

/**
 * Analyze a variable and provide a formated string representation of its type and data.
 */
final class VarDump implements VarDumpContract
{
    private FormatterContract $formatter;

    /** @var array [className,] */
    private array $dontDump = [];

    private string $output = '';

    private int $indent = 0;

    private string $indentString = '';

    private int $depth = 0;

    private $var;

    private $val;

    private string $type;

    private string $info;

    private string $template;

    /**
     * Creates a new instance.
     *
     * @param FormatterContract $formatter A VarDump formatter
     */
    public function __construct(FormatterContract $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function formatter(): FormatterContract
    {
        return $this->formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function withDontDump(string ...$dontDump): VarDumpContract
    {
        $new = clone $this;
        $new->dontDump = $dontDump;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function dontDump(): array
    {
        return $this->dontDump;
    }

    /**
     * {@inheritdoc}
     */
    public function withVar($var): VarDumpContract
    {
        $new = clone $this;
        ++$new->depth;
        $new->var = $var;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        return $this->var;
    }

    /**
     * {@inheritdoc}
     */
    public function withIndent(int $indent): VarDumpContract
    {
        $new = clone $this;
        $new->indent = $indent;
        $new->indentString = $new->formatter
            ->getIndent($indent);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function indent(): int
    {
        return $this->indent;
    }

    /**
     * {@inheritdoc}
     */
    public function withDepth(int $depth): VarDumpContract
    {
        $new = clone $this;
        $new->depth = $depth;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function depth(): int
    {
        return $this->depth;
    }

    /**
     * {@inheritdoc}
     */
    public function process(): VarDumpContract
    {
        $this->val = null;
        $this->type = varType($this->var);
        $this->handleType();
        $this->setTemplate();
        $this->setOutput();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function indentString(): string
    {
        return $this->indentString;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->output;
    }

    private function handleType(): void
    {
        switch ($this->type) {
            case static::TYPE_BOOLEAN:
                $processor = new BooleanProcessor($this->var);
                break;
            case static::TYPE_ARRAY:
                ++$this->indent;
                $processor = new ArrayProcessor($this->var, $this);
                break;
            case static::TYPE_OBJECT:
                ++$this->indent;
                $processor = new ObjectProcessor($this->var, $this);
                break;
            default:
                $processor = new ScalarProcessor($this->var, $this);
                break;
        }
        $this->val .= $processor->val();
        $this->info = $processor->info();
        $this->handleInfo();
    }

    private function handleInfo(): void
    {
        if ('' !== $this->info) {
            if (strpos($this->info, '=')) {
                $this->info = $this->formatter->getEmphasis("($this->info)");
            } else {
                $this->info = $this->formatter->getWrap(static::_CLASS, $this->info);
            }
        }
    }

    private function setTemplate(): void
    {
        switch ($this->type) {
            case static::TYPE_ARRAY:
            case static::TYPE_OBJECT:
                $this->template = '%type% %info% %val%';
                break;
            default:
                $this->template = '%type% %val% %info%';
                break;
        }
    }

    private function setOutput(): void
    {
        $message = $this->template;
        foreach (['info', 'val'] as $property) {
            if ('' == $this->$property) {
                $message = str_replace('%' . $property . '%', null, $message);
                $message = preg_replace('!\s+!', ' ', $message);
                $message = trim($message);
            }
        }
        $this->output = strtr($message, [
            '%type%' => $this->formatter->getWrap($this->type, $this->type),
            '%val%' => $this->val,
            '%info%' => $this->info,
        ]);
    }
}
