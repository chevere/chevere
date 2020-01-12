<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Outputters;

use Chevere\Components\VarDump\Contracts\OutputterContract;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

final class ConsoleOutputter extends AbstractOutputter
{
    private ConsoleOutputInterface $consoleOutput;

    private string $outputHr = '';

    public function __construct()
    {
        $this->outputHr = '<hr>' . str_repeat('-', 60) . '</>';
        $this->consoleOutput = new ConsoleOutput();
        $this->consoleOutput->setFormatter(new OutputFormatter(true));
        $this->consoleOutput->getFormatter()->setStyle('block', new OutputFormatterStyle('red', 'black'));
        $this->consoleOutput->getFormatter()->setStyle('dumper', new OutputFormatterStyle('blue', null, ['bold']));
        $this->consoleOutput->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue', null));
    }

    public function prepare(): OutputterContract
    {
        $aux = 0;
        $maker =
            (
                isset($this->dumper->debugBacktrace()[$aux]['class'])
                ? $this->dumper->debugBacktrace()[$aux]['class'] . $this->dumper->debugBacktrace()[$aux]['type']
                : null
            )
            . $this->dumper->debugBacktrace()[$aux]['function'] . '()';
        $this->consoleOutput->writeln(['', '<dumper>' . $maker . '</>', $this->outputHr]);

        return $this;
    }

    public function printOutput(): void
    {
        $this->consoleOutput->writeln($this->output, ConsoleOutput::OUTPUT_RAW);
        $this->consoleOutput->writeln($this->outputHr);
    }
}
