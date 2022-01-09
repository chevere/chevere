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
        $outputter->writeCallerFile($this->formatter);
        $this->handleArgs();
        $outputter->tearDown();
    }

    private function handleArgs(): void
    {
        $aux = 0;
        foreach ($this->vars as $name => $value) {
            $aux++;
            $varDumper = new VarDumper(
                $this->writer,
                $this->formatter,
                new VarDumpable($value)
            );
            $this->writer->write(
                str_repeat("\n", (int) ($aux === 1 ?: 2))
                . "Arg:" . (string) $name . ' '
            );
            $varDumper->withProcess();
        }
    }
}
