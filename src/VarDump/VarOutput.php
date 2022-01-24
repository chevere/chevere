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

use Chevere\VarDump\Interfaces\VarDumpFormatInterface;
use Chevere\VarDump\Interfaces\VarDumpOutputInterface;
use Chevere\VarDump\Interfaces\VarOutputInterface;
use Chevere\Writer\Interfaces\WriterInterface;

final class VarOutput implements VarOutputInterface
{
    public function __construct(
        private WriterInterface $writer,
        private array $trace,
        private VarDumpFormatInterface $format,
    ) {
    }

    public function process(VarDumpOutputInterface $output, ...$vars): void
    {
        $output->setUp($this->writer, $this->trace);
        $output->prepare();
        $output->writeCallerFile($this->format);
        $this->handleArgs($vars);
        $output->tearDown();
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
