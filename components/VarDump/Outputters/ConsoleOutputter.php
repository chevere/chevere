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

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Symfony\Component\Console\Output\ConsoleOutput;

final class ConsoleOutputter extends PlainOutputter
{
    private string $outputHr;

    public function prepare(): void
    {
        $this->outputHr = (new ConsoleColor)->apply(
            'blue',
            '------------------------------------------------------------'
        );
        $bt = $this->debugBacktrace[0];
        $caller = '';
        if ($bt['class'] ?? null) {
            $caller .= $bt['class'] . $bt['type'];
        }
        if ($bt['function'] ?? null) {
            $caller .= $bt['function'] . '()';
        }
        $this->streamWriter->write(
            "\n" . (new ConsoleColor)->apply(['bold', 'red'], $caller)
            . "\n" . $this->outputHr . "\n"
        );
    }

    public function callback(): void
    {
        $this->streamWriter->write("\n" . $this->outputHr . "\n");
    }
}
