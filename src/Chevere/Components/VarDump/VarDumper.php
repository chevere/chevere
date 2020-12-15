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

use Chevere\Interfaces\Type\TypeInterface;
use Chevere\Interfaces\VarDump\VarDumpableInterface;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatterInterface;
use Chevere\Interfaces\Writer\WriterInterface;
use Ds\Set;

final class VarDumper implements VarDumperInterface
{
    private WriterInterface $writer;

    private VarDumpFormatterInterface $formatter;

    private VarDumpableInterface $dumpable;

    private int $indent = 0;

    private string $indentString = '';

    private int $depth = -1;

    public Set $known;

    public function __construct(
        WriterInterface $writer,
        VarDumpFormatterInterface $formatter,
        VarDumpableInterface $dumpable
    ) {
        $this->writer = $writer;
        $this->dumpable = $dumpable;
        $this->formatter = $formatter;
        $this->known = new Set();
        ++$this->depth;
    }

    public function writer(): WriterInterface
    {
        return $this->writer;
    }

    public function formatter(): VarDumpFormatterInterface
    {
        return $this->formatter;
    }

    public function dumpable(): VarDumpableInterface
    {
        return $this->dumpable;
    }

    public function withIndent(int $indent): VarDumperInterface
    {
        $new = clone $this;
        $new->indent = $indent;
        $new->indentString = $new->formatter->indent($indent);

        return $new;
    }

    public function indent(): int
    {
        return $this->indent;
    }

    public function indentString(): string
    {
        return $this->indentString;
    }

    public function withDepth(int $depth): VarDumperInterface
    {
        $new = clone $this;
        $new->depth = $depth;

        return $new;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function withKnownObjects(Set $known): VarDumperInterface
    {
        $new = clone $this;
        $new->known = $known;

        return $new;
    }

    public function known(): Set
    {
        return $this->known;
    }

    public function withProcess(): VarDumperInterface
    {
        $new = clone $this;
        $processorName = $new->dumpable->processorName();
        if (in_array($new->dumpable->type(), [TypeInterface::ARRAY, TypeInterface::OBJECT])) {
            ++$new->indent;
        }
        (new $processorName($new))->write();

        return $new;
    }
}
