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

use Chevere\Interfaces\VarDump\VarDumpFormatInterface;
use Chevere\Interfaces\VarDump\VarDumpOutputInterface;
use Chevere\Interfaces\VarDump\VarOutputInterface;
use Chevere\Interfaces\Writer\WriterInterface;

final class VarOutput implements VarOutputInterface
{
    public function __construct(
        private WriterInterface $writer,
        private array $backtrace,
        private VarDumpFormatInterface $format,
    ) {
    }

    public function process(VarDumpOutputInterface $outputter, ...$vars): void
    {
        $outputter->setUp($this->writer, $this->backtrace);
        $outputter->prepare();
        $outputter->writeCallerFile($this->format);
        $this->handleArgs($vars);
        $outputter->tearDown();
    }

    private function handleArgs(array $vars): void
    {
        $aux = 0;
        foreach ($vars as $name => $value) {
            $aux++;
            $varDumper = new VarDumper(
                $this->writer,
                $this->format,
                new VarDumpable($value)
            );
            $this->writer->write(
                str_repeat("\n", (int) ($aux === 1 ?: 2))
                . "Arg:" . strval($name) . ' '
            );
            $varDumper->withProcess();
        }
    }
}