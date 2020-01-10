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

use Chevere\Components\VarDump\Contracts\DumperContract;
use Chevere\Components\VarDump\Contracts\OutputterContract;
use Chevere\Components\VarDump\Outputter;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use function ChevereFn\stringStartsWith;

final class DumperOutputter extends Outputter
{
    private ConsoleOutputInterface $consoleOutput;

    private string $outputHr = '';

    public function prepare(): OutputterContract
    {
        if ($this->dumper->isCli()) {
            $this->handleConsole();
        } else {
            $this->handleHtml();
        }

        return $this;
    }

    public function toString(): string
    {
        return $this->output;
    }

    public function printOutput(): void
    {
        if (isset($this->consoleOutput)) {
            $this->consoleOutput->writeln($this->output, ConsoleOutput::OUTPUT_RAW);
            isset($this->outputHr) ? $this->consoleOutput->writeln($this->outputHr) : null;

            return;
        }
    }

    final private function handleConsole(): void
    {
        $this->consoleOutput = new ConsoleOutput();
        $outputFormatter = new OutputFormatter(true);
        $this->consoleOutput->setFormatter($outputFormatter);
        $this->consoleOutput->getFormatter()->setStyle('block', new OutputFormatterStyle('red', 'black'));
        $this->consoleOutput->getFormatter()->setStyle('dumper', new OutputFormatterStyle('blue', null, ['bold']));
        $this->consoleOutput->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue'));
        $this->outputHr = '<hr>' . str_repeat('-', 60) . '</>';
        $this->consoleOutput->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue', null));
        $maker =
            (
                isset($this->dumper->debugBacktrace()[0]['class'])
                ? $this->dumper->debugBacktrace()[0]['class'] . $this->dumper->debugBacktrace()[0]['type']
                : null
            )
            . $this->dumper->debugBacktrace()[0]['function'] . '()';
        $this->consoleOutput->writeln(['', '<dumper>' . $maker . '</>', $this->outputHr]);
    }

    final private function handleHtml(): void
    {
        if (false === headers_sent()) {
            $this->output .= '<html style="background: ' . $this->dumper::BACKGROUND_SHADE . ';"><head></head><body>';
        }
        $this->output .= '<pre style="' . $this->dumper::STYLE . '">';
    }
}
