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
use Chevere\Components\VarDump\VarProcess;
use Chevere\Components\Writers\Interfaces\WriterInterface;

class PlainOutputter implements OutputterInterface
{
    const OFFSET = 1;

    protected WriterInterface $streamWriter;

    public function prepare(): void
    {
    }

    public function callback(): void
    {
    }

    final public function __construct(
        WriterInterface $streamWriter,
        array $debugBacktrace,
        FormatterInterface $formatter,
        ...$vars
    ) {
        $this->streamWriter = $streamWriter;
        $this->debugBacktrace = $debugBacktrace;
        $this->formatter = $formatter;
        $this->vars = $vars;
        $this->prepare();
        $this->handleClass();
        $this->streamWriter->write(
            $this->formatter
                ->highlight(
                    '_function',
                    $this->debugBacktrace[self::OFFSET]['function'] . '()'
                )
        );
        $this->handleFile();
        $this->handleArgs();
        $this->callback();
    }

    final private function handleClass(): void
    {
        $item = $this->debugBacktrace[self::OFFSET];
        $class = $item['class'] ?? null;
        if ($class !== null) {
            $type = $item['type'];
            $this->streamWriter->write(
                $this->formatter
                    ->highlight('_class', $class) . $type
            );
        }
    }

    final private function handleFile(): void
    {
        $pos = self::OFFSET - 1;
        $item = $this->debugBacktrace[$pos];
        if ($item !== null && isset($item['file'])) {
            $this->streamWriter->write(
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
            $varProcess = new VarProcess(
                $this->streamWriter,
                new VarDumpeable($value),
                $this->formatter
            );
            $this->streamWriter->write("\n\nArg#" . $pos . ' ');
            $varProcess->withProcessor();
            ++$pos;
        }
    }
}
