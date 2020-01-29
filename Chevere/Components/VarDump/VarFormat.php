<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump;

use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarDump\Interfaces\DumpeableInterface;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarFormatInterface;

/**
 * The Chevere VarFormat.
 *
 * Analyze a Dumpeable and provide a formated string representation of its type and data.
 */
final class VarFormat implements VarFormatInterface
{
    private DumpeableInterface $dumpeable;

    private FormatterInterface $formatter;

    private ProcessorInterface $processor;

    /** @var array [className,] */
    // private array $dontDump = [];

    private string $output = '';

    private int $indent = 0;

    private string $indentString = '';

    private int $depth = -1;

    private string $val = '';

    private string $info;

    /**
     * Creates a new instance.
     *
     * @param FormatterInterface $formatter A VarDump formatter
     */
    public function __construct(DumpeableInterface $dumpeable, FormatterInterface $formatter)
    {
        $this->dumpeable = $dumpeable;
        $this->formatter = $formatter;
        ++$this->depth;
    }

    public function dumpeable(): DumpeableInterface
    {
        return $this->dumpeable;
    }

    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

    // public function withDontDump(string ...$dontDump): VarInfoInterface
    // {
    //     $new = clone $this;
    //     $new->dontDump = $dontDump;

    //     return $new;
    // }

    // public function dontDump(): array
    // {
    //     return $this->dontDump;
    // }

    public function withIndent(int $indent): VarFormatInterface
    {
        $new = clone $this;
        $new->indent = $indent;
        $new->indentString = $new->formatter
            ->indent($indent);

        return $new;
    }

    public function indent(): int
    {
        return $this->indent;
    }

    public function withDepth(int $depth): VarFormatInterface
    {
        $new = clone $this;
        $new->depth = $depth;

        return $new;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function withProcess(): VarFormatInterface
    {
        $new = clone $this;

        $new->setProcessor();
        $new->val .= $new->processor->value();
        $new->setInfo();
        $new->setOutput();

        return $new;
    }

    public function indentString(): string
    {
        return $this->indentString;
    }

    public function toString(): string
    {
        return $this->output;
    }

    private function setProcessor(): void
    {
        if (in_array($this->dumpeable()->type(), [TypeInterface::ARRAY, TypeInterface::OBJECT])) {
            ++$this->indent;
        }
        $processorName = $this->dumpeable()->processorName();
        $this->processor = new $processorName($this);
    }

    private function setInfo(): void
    {
        $this->info = $this->processor->info();
        if ('' !== $this->info) {
            if (strpos($this->info, '=')) {
                $this->info = $this->formatter->emphasis("($this->info)");
            } else {
                $this->info = $this->formatter->highlight(VarFormatInterface::_CLASS, $this->info);
            }
        }
    }

    private function setOutput(): void
    {
        $message = $this->dumpeable()->template();
        foreach (['info', 'val'] as $property) {
            if ('' == $this->$property) {
                $message = str_replace('%' . $property . '%', null, $message);
                $message = preg_replace('!\s+!', ' ', $message);
                $message = trim($message);
            }
        }
        $this->output = strtr($message, [
            '%type%' => $this->formatter->highlight($this->dumpeable()->type(), $this->dumpeable()->type()),
            '%val%' => $this->val,
            '%info%' => $this->info,
        ]);
    }
}
