<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\VarDump;

use const Chevere\CLI;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Chevere\Path;
use Chevere\Utility\Str;

/**
 * Dumps information about one or more variables. CLI/HTML aware.
 */
final class Dumper
{
    const BACKGROUND = '#132537';
    const BACKGROUND_SHADE = '#132537';
    const STYLE = 'font: 14px Consolas, monospace, sans-serif; line-height: 1.2; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: '.self::BACKGROUND.'; display: block; text-align: left; border: none; border-radius: 4px;';

    private $vars;

    /** @var int */
    private $numArgs;

    /** @var ConsoleOutputInterface */
    private $consoleOutput;

    /** @var string */
    private $output;

    /** @var string */
    private $outputHr;

    /** @var array */
    private $debugBacktrace;

    /** @var string */
    private $caller;

    /** @var string */
    private $callerFilepath;

    /** @var int */
    private $offset = 1;

    /** @var string */
    private $varDump;

    public function __construct(...$vars)
    {
        $this->varDump = VarDump::RUNTIME;
        $this->vars = $vars;
        $this->numArgs = func_num_args();
        if (0 == $this->numArgs) {
            return;
        }
        $this->debugBacktrace = debug_backtrace();
        $this->caller = $this->debugBacktrace[0];
        $this->handleDebugBacktrace();
        $this->setCallerFilepath($this->debugBacktrace[0]['file']);
        $this->handleSelfCaller();
        $this->output = null;
        if ($this->varDump == ConsoleVarDump::class) {
            $this->handleConsoleOutput();
        } else {
            $this->handleHtmlOutput();
        }
        $this->handleClass();
        $this->appendFunction($this->debugBacktrace[$this->offset]['function']);
        $this->handleFile();
        $this->output .= "\n\n";
        $this->handleArgs();
        $this->output = trim($this->output);
        $this->handleProccessOutput();
    }

    public static function dump(...$vars): void
    {
        new static(...$vars);
    }

    /**
     * Dumps information about one or more variables and die().
     */
    public static function dd(...$vars)
    {
        static::dump(...$vars);
        die(1);
    }

    private function handleDebugBacktrace(): void
    {
        while (isset($this->debugBacktrace[0]['file']) && __FILE__ == $this->debugBacktrace[0]['file']) {
            $this->shiftDebugBacktrace();
            $this->caller = $this->debugBacktrace[0];
        }
    }

    private function setCallerFilepath(string $filepath): void
    {
        $this->callerFilepath = Path::normalize($filepath);
    }

    private function handleSelfCaller(): void
    {
        if (Str::endsWith('resources/functions/dump.php', $this->callerFilepath) && __CLASS__ == $this->debugBacktrace[0]['class'] && in_array($this->debugBacktrace[0]['function'], ['dump', 'dd'])) {
            $this->shiftDebugBacktrace();
        }
    }

    private function shiftDebugBacktrace(): void
    {
        array_shift($this->debugBacktrace);
    }

    private function handleConsoleOutput(): void
    {
        $this->consoleOutput = new ConsoleOutput();
        $outputFormatter = new OutputFormatter(true);
        $this->consoleOutput->setFormatter($outputFormatter);
        $this->consoleOutput->getFormatter()->setStyle('block', new OutputFormatterStyle('red', 'black'));
        $this->consoleOutput->getFormatter()->setStyle('dumper', new OutputFormatterStyle('blue', null, ['bold']));
        $this->consoleOutput->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue'));
        $this->outputHr = '<hr>'.str_repeat('-', 60).'</>';
        $this->consoleOutput->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue', null));
        $maker = (isset($this->caller['class']) ? $this->caller['class'].$this->caller['type'] : null).$this->caller['function'].'()';
        $this->consoleOutput->writeln(['', '<dumper>'.$maker.'</>', $this->outputHr]);
    }

    private function handleHtmlOutput(): void
    {
        if (false === headers_sent()) {
            $this->appendHtmlOpenBody();
        }
        $this->appendStyle();
    }

    private function appendHtmlOpenBody(): void
    {
        $this->output .= '<html style="background: '.static::BACKGROUND_SHADE.';"><head></head><body>';
    }

    private function appendStyle(): void
    {
        $this->output .= '<pre style="'.static::STYLE.'">';
    }

    private function handleClass(): void
    {
        if (isset($this->debugBacktrace[1]['class'])) {
            $class = $this->debugBacktrace[$this->offset]['class'];
            if (Str::startsWith('class@anonymous', $class)) {
                $class = explode('0x', $class)[0];
            }
            $this->appendClass($class, $this->debugBacktrace[$this->offset]['type']);
        }
    }

    private function appendClass(string $class, string $type): void
    {
        $this->output .= $this->varDump::wrap('_class', $class).$type;
    }

    private function appendFunction(string $function): void
    {
        $this->output .= $this->varDump::wrap('_function', $function.'()');
    }

    private function handleFile(): void
    {
        if (isset($this->debugBacktrace[0]['file'])) {
            $this->appendFilepath($this->debugBacktrace[0]['file'], $this->debugBacktrace[0]['line']);
        }
    }

    private function appendFilepath(string $file, int $line): void
    {
        $this->output .= "\n".$this->varDump::wrap('_file', Path::normalize($file).':'.$line);
    }

    private function handleArgs(): void
    {
        $pos = 1;
        foreach ($this->vars as $value) {
            $this->appendArg($pos, $value);
            ++$pos;
        }
        // $this->output = trim($this->output, '\n');
    }

    private function appendArg(int $pos, $value): void
    {
        $this->output .= 'Arg#'.$pos.' '.$this->varDump::out($value, 0)."\n\n";
    }

    private function handleProccessOutput(): void
    {
        if (isset($this->consoleOutput)) {
            $this->processConsoleOutput();
        } else {
            $this->processPrintOutput();
        }
    }

    private function processConsoleOutput(): void
    {
        $this->consoleOutput->writeln($this->output, ConsoleOutput::OUTPUT_RAW);
        isset($this->outputHr) ? $this->consoleOutput->writeln($this->outputHr) : null;
    }

    private function processPrintOutput(): void
    {
        echo $this->output;
    }
}
