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

use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;
use Chevere\Components\VarDump\Interfaces\VarOutputterInterface;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Writers\Interfaces\WriterInterface;

/**
 * The Chevere VarOutputter.
 * Writes information about a variable.
 */
final class VarOutputter implements VarOutputterInterface
{
    private WriterInterface $writer;

    private array $debugBacktrace;

    private FormatterInterface $formatter;

    private $vars;

    public function __construct(
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

    public function process(OutputterInterface $outputter): void
    {
        $outputter->setUp($this->writer, $this->debugBacktrace);
        $outputter->prepare();
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
        $outputter->callback();
    }

    private function handleClass(): void
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

    private function writeCallerFile(): void
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

    private function handleArgs(): void
    {
        $pos = 1;
        foreach ($this->vars as $value) {
            $varDumper = new VarDumper(
                $this->writer,
                $this->formatter,
                new VarDumpeable($value)
            );
            $this->writer->write("\n\nArg#" . $pos . ' ');
            $varDumper->withProcessor();
            ++$pos;
        }
    }
}
