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
        private array $backtrace,
        private VarDumpFormatterInterface $formatter,
        ...$vars
    ) {
        $this->vars = $vars;
    }

    public function process(VarDumpOutputterInterface $outputter): void
    {
        $outputter->setUp($this->writer, $this->backtrace);
        $outputter->prepare();
        $this->handleClassFunction();
        $outputter->writeCallerFile($this->formatter);
        $this->handleArgs();
        $outputter->tearDown();
    }

    private function handleClassFunction(): void
    {
        $item = $this->backtrace[1] ?? [];
        $class = $item['class'] ?? null;
        if (isset($item, $class)) {
            $this->writer->write("\n");
            $type = $item['type'];
            $this->writer->write(
                $this->formatter->highlight(VarDumperInterface::CLASS_REG, $class)
                . $type
            );
        }
        $debugFn = $this->backtrace[1]['function'] ?? null;
        if ($debugFn !== null) {
            $debugFn .= '()';
            $this->writer->write(
                ($class == null ? "\n" : '')
                . $this->formatter->highlight(VarDumperInterface::FUNCTION, $debugFn)
                . "\n"
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
            $this->writer->write("\nArg:" . (string) $name . ' ');
            $varDumper->withProcess();
        }
    }
}
