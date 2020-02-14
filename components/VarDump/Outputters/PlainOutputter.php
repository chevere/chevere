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

use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Writers\Interfaces\WriterInterface;

class PlainOutputter implements OutputterInterface
{
    const OFFSET = 1;

    protected WriterInterface $writer;

    public function prepare(): void
    {
    }

    public function callback(): void
    {
    }

    final public function __construct(
        WriterInterface $writer,
        array $debugBacktrace,
        FormatterInterface $formatter,
        ...$vars
    ) {
        $this->writer = $writer;
        $this->debugBacktrace = $debugBacktrace;
        $this->formatter = $formatter;
        $this->vars = $vars;
    }

    final public function process(): void
    {
        $this->prepare();
        $this->handleClass();
        $this->writer->write(
            $this->formatter
                ->highlight(
                    '_function',
                    $this->debugBacktrace[1]['function'] . '()'
                )
        );
        $this->writeCallerFile();
        $this->handleArgs();
        $this->callback();
    }

    final private function handleClass(): void
    {
        $item = $this->debugBacktrace[1];
        $class = $item['class'] ?? null;
        if ($class !== null) {
            $type = $item['type'];
            $this->writer->write(
                $this->formatter
                    ->highlight('_class', $class) . $type
            );
        }
    }

    final private function writeCallerFile(): void
    {
        $item = $this->debugBacktrace[0];
        if ($item !== null && isset($item['file'])) {
            $this->writer->write(
                "\n"
                . $this->formatter
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
        foreach ($this->vars as $value) {
            $varProcess = new VarDumper(
                $this->writer,
                new VarDumpeable($value),
                $this->formatter
            );
            $this->writer->write("\n\nArg#" . $pos . ' ');
            $varProcess->withProcessor();
            ++$pos;
        }
    }
}
