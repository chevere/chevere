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

use Chevere\Components\VarDump\VarDumpable;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Interfaces\VarDump\FormatterInterface;
use Chevere\Interfaces\VarDump\OutputterInterface;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Interfaces\VarDump\VarOutputterInterface;
use Chevere\Interfaces\Writers\WriterInterface;

/**
 * Writes information about a variable.
 */
final class VarOutputter implements VarOutputterInterface
{
    private WriterInterface $writer;

    private array $debugBacktrace;

    private FormatterInterface $formatter;

    private array $vars;

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
        $this->handleClassFunction();
        $this->writeCallerFile();
        $this->handleArgs();
        $outputter->callback();
    }

    private function handleClassFunction(): void
    {
        $item = $this->debugBacktrace[1] ?? null;
        $class = $item['class'] ?? null;
        if ($class !== null) {
            $this->writer->write("\n");
            $type = $item['type'];
            $this->writer->write(
                $this->formatter->highlight(VarDumperInterface::CLASS_REG, $class)
                . $type
            );
        }
        $debugFn = $this->debugBacktrace[1]['function'] ?? null;
        if ($debugFn !== null) {
            $debugFn .= '()';
            $this->writer->write(
                ($class === null ? "\n" : '')
                . $this->formatter->highlight(VarDumperInterface::FUNCTION, (string) $debugFn)
            );
        }
    }

    private function writeCallerFile(): void
    {
        $item = $this->debugBacktrace[0] ?? null;
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
                new VarDumpable($value)
            );
            $this->writer->write("\n\nArg#" . $pos . ' ');
            $varDumper->withProcessor();
            ++$pos;
        }
    }
}
