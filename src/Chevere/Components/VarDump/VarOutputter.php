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

use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatterInterface;
use Chevere\Interfaces\VarDump\VarDumpOutputterInterface;
use Chevere\Interfaces\VarDump\VarOutputterInterface;
use Chevere\Interfaces\Writer\WriterInterface;

final class VarOutputter implements VarOutputterInterface
{
    private array $vars;

    public function __construct(
        private WriterInterface $writer,
        private array $debugBacktrace,
        private VarDumpFormatterInterface $formatter,
        ...$vars
    ) {
        $this->vars = $vars;
    }

    public function process(VarDumpOutputterInterface $outputter): void
    {
        $outputter->setUp($this->writer, $this->debugBacktrace);
        $outputter->prepare();
        $this->handleClassFunction();
        $this->writeCallerFile();
        $this->handleArgs();
        $outputter->tearDown();
    }

    private function handleClassFunction(): void
    {
        $item = $this->debugBacktrace[1] ?? [];
        $class = $item['class'] ?? null;
        if (isset($item, $class)) {
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
                . $this->formatter->highlight(VarDumperInterface::FUNCTION, $debugFn)
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
        foreach ($this->vars as $name => $value) {
            $varDumper = new VarDumper(
                $this->writer,
                $this->formatter,
                new VarDumpable($value)
            );
            $this->writer->write("\n\nArg:" . (string) $name . ' ');
            $varDumper->withProcess();
        }
    }
}
