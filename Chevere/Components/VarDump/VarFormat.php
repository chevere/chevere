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
use Chevere\Components\VarDump\Interfaces\VarDumpeableInterface;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\ProcessorInterface;
use Chevere\Components\VarDump\Interfaces\VarFormatInterface;
use function ChevereFn\stringStartsWith;

/**
 * The Chevere VarFormat.
 *
 * Analyze a Dumpeable and provide a formated string representation of its type and data.
 */
final class VarFormat implements VarFormatInterface
{
    private VarDumpeableInterface $dumpeable;

    private FormatterInterface $formatter;

    private ProcessorInterface $processor;

    private string $output = '';

    private int $indent = 0;

    private string $indentString = '';

    private int $depth = -1;

    private string $val = '';

    private string $info = '';

    public array $known = [];

    /**
     * Creates a new instance.
     *
     * @param FormatterInterface $formatter A VarDump formatter
     */
    public function __construct(VarDumpeableInterface $dumpeable, FormatterInterface $formatter)
    {
        $this->dumpeable = $dumpeable;
        $this->formatter = $formatter;
        ++$this->depth;
    }

    public function dumpeable(): VarDumpeableInterface
    {
        return $this->dumpeable;
    }

    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

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

    public function withKnown(array $known): VarFormatInterface
    {
        $new = clone $this;
        $new->known = $known;

        return $new;
    }

    public function known(): array
    {
        return $this->known;
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
        $processorName = $this->dumpeable()->processorName();
        if (in_array($this->dumpeable()->type(), [TypeInterface::ARRAY, TypeInterface::OBJECT])) {
            ++$this->indent;
        }
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
        $table = [
            '%type%' => $this->formatter->highlight($this->dumpeable()->type(), $this->dumpeable()->type()),
            '%val%' => $this->val,
            '%info%' => $this->info,
        ];

        $template = $this->dumpeable()->template();
        $aux = [];
        foreach ($template as $tagName) {
            $value = $table[$tagName];
            if ('' == $value) {
                continue;
            }
            $aux[] = $tagName;
        }
        $message = implode(' ', $aux);
        if (stringStartsWith("\n", $this->val)) {
            $message = str_replace(' %val%', '%val%', $message);
        }
        $this->output = $message;
        $this->output = strtr($message, $table);
    }
}
