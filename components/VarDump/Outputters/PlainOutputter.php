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

namespace Chevere\Components\VarDump\Outputters;

use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;
use Chevere\Components\VarDump\VarFormat;
use Chevere\Components\Writers\Interfaces\StreamWriterInterface;

class PlainOutputter implements OutputterInterface
{
    protected VarDumperInterface $varDumper;

    protected StreamWriterInterface $streamWriter;

    public function prepare(): void
    {
    }

    public function callback(): void
    {
    }

    final public function __construct(VarDumperInterface $varDumper, StreamWriterInterface $streamWriter)
    {
        $this->varDumper = $varDumper;
        $this->streamWriter = $streamWriter;
    }

    final public function varDumper(): VarDumperInterface
    {
        return $this->varDumper;
    }

    final public function streamWriter(): StreamWriterInterface
    {
        return $this->streamWriter;
    }

    final public function emit(): void
    {
        $new = clone $this;
        $new->prepare();
        $new->handleClass();
        $new->streamWriter->write(
            $new->varDumper->formatter()
                ->highlight(
                    '_function',
                    $new->varDumper->debugBacktrace()[VarDumperInterface::OFFSET]['function'] . '()'
                )
        );
        $new->handleFile();
        // $new->streamWriter->write("\n\n");
        $new->handleArgs();
        $new->callback();
    }

    final private function handleClass(): void
    {
        $item = $this->varDumper->debugBacktrace()[VarDumperInterface::OFFSET];
        $class = $item['class'] ?? null;
        if ($class !== null) {
            $type = $item['type'];
            $this->streamWriter->write(
                $this->varDumper->formatter()
                    ->highlight('_class', $class) . $type
            );
        }
    }

    final private function handleFile(): void
    {
        $pos = VarDumperInterface::OFFSET - 1;
        $item = $this->varDumper->debugBacktrace()[$pos];
        if ($item !== null && isset($item['file'])) {
            $this->streamWriter->write(
                "\n"
                . $this->varDumper->formatter()
                    ->highlight(
                        '_file',
                        $item['file'] . ':' . $item['line']
                    )
            );
        }
    }

    final private function handleArgs(): void
    {
        $pos = 1;
        foreach ($this->varDumper->vars() as $value) {
            $this->appendArg($pos, $value);
            ++$pos;
        }
    }

    final private function appendArg(int $pos, $value): void
    {
        $varDumpeable = new VarDumpeable($value);
        $varFormat = new VarFormat($varDumpeable, $this->varDumper->formatter());
        $this->streamWriter->write(
            'Arg#' . $pos . ' '
        );
        $varFormat->withProcess();
        $this->streamWriter->write("\n\n");
    }
}
