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

namespace Chevere\VarDump;

use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarDump\Interfaces\VarDumpableInterface;
use Chevere\VarDump\Interfaces\VarDumperInterface;
use Chevere\VarDump\Interfaces\VarDumpFormatInterface;
use Chevere\Writer\Interfaces\WriterInterface;
use Ds\Set;

final class VarDumper implements VarDumperInterface
{
    public Set $known;

    private int $indent = 0;

    private string $indentString = '';

    private int $depth = -1;

    public function __construct(
        private WriterInterface $writer,
        private VarDumpFormatInterface $format,
        private VarDumpableInterface $dumpable
    ) {
        $this->known = new Set();
        ++$this->depth;
    }

    public function writer(): WriterInterface
    {
        return $this->writer;
    }

    public function format(): VarDumpFormatInterface
    {
        return $this->format;
    }

    public function dumpable(): VarDumpableInterface
    {
        return $this->dumpable;
    }

    public function withIndent(int $indent): VarDumperInterface
    {
        $new = clone $this;
        $new->indent = $indent;
        $new->indentString = $new->format->indent($indent);

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
        if (in_array($new->dumpable->type(), [TypeInterface::ARRAY, TypeInterface::OBJECT], true)) {
            ++$new->indent;
        }
        (new $processorName($new))->write();

        return $new;
    }
}
