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

final class ConsoleOutputter extends AbstractOutputter
{
    const OUTPUT_HR = '------------------------------------------------------------';

    private string $outputHr;

    public function __construct()
    {
        $this->consoleOutput = new ConsoleOutput();
        $this->outputHr = (new ConsoleColor)->apply('blue', self::OUTPUT_HR);
    }

    public function prepare(string $output): string
    {
        $aux = 0;
        $maker =
            (
                isset($this->dumper->debugBacktrace()[$aux]['class'])
                ? $this->dumper->debugBacktrace()[$aux]['class'] . $this->dumper->debugBacktrace()[$aux]['type']
                : null
            )
            . $this->dumper->debugBacktrace()[$aux]['function'] . '()';

        return $output .= "\n" . (new ConsoleColor)->apply(['bold', 'red'], $maker) . "\n" . $this->outputHr . "\n";
    }

    public function callback(string $output): string
    {
        return $output . "\n" . $this->outputHr;
    }
}
