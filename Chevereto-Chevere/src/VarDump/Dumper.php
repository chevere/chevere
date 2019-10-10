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
use Chevere\Path\Path;
use Chevere\Str\Str;
use Chevere\VarDump\Formatters\DumperFormatter;
use Chevere\Contracts\VarDump\FormatterContract;

use function ChevereFn\stringEndsWith;
use function ChevereFn\stringStartsWith;

/**
 * Dumps information about one or more variables. CLI/HTML aware.
 */
class Dumper
{
    const BACKGROUND = '#132537';
    const BACKGROUND_SHADE = '#132537';
    const STYLE = 'font: 14px Consolas, monospace, sans-serif; line-height: 1.2; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: ' . self::BACKGROUND . '; display: block; text-align: left; border: none; border-radius: 4px;';

    const OFFSET = 1;


    /** @var FormatterContract */
    protected $formatter;

    /** @var VarDump */
    private $varDump;

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

    final public function __construct()
    {
        $this->formatter = $this->getFormatter();
    }

    protected function getFormatter(): FormatterContract
    {
        return new DumperFormatter();
    }

    final private function dumper(...$vars): void
    {
        $this->varDump = new VarDump($this->formatter);
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
        if (CLI) {
            $this->handleConsoleOutput();
        } else {
            $this->handleHtmlOutput();
        }
        $this->handleClass();
        $this->appendFunction($this->debugBacktrace[static::OFFSET]['function']);
        $this->handleFile();
        $this->output .= "\n\n";
        $this->handleArgs();
        $this->output = trim($this->output);
        $this->handleProccessOutput();
    }

    final public static function dump(...$vars): void
    {
        $new = new static();
        $new->dumper(...$vars);
    }

    /**
     * Dumps information about one or more variables and die(0).
     */
    final public static function dd(...$vars)
    {
        static::dump(...$vars);
        die(0);
    }

    final private function handleDebugBacktrace(): void
    {
        while (isset($this->debugBacktrace[0]['file']) && __FILE__ == $this->debugBacktrace[0]['file']) {
            $this->shiftDebugBacktrace();
            $this->caller = $this->debugBacktrace[0];
        }
    }

    final private function setCallerFilepath(string $filepath): void
    {
        $this->callerFilepath = Path::normalize($filepath);
    }

    final private function handleSelfCaller(): void
    {
        if (
            stringEndsWith('resources/functions/dump.php', $this->callerFilepath)
            && __CLASS__ == $this->debugBacktrace[0]['class']
            && in_array($this->debugBacktrace[0]['function'], ['dump', 'dd'])
        ) {
            $this->shiftDebugBacktrace();
        }
    }

    final private function shiftDebugBacktrace(): void
    {
        array_shift($this->debugBacktrace);
    }

    final private function handleConsoleOutput(): void
    {
        $this->consoleOutput = new ConsoleOutput();
        $outputFormatter = new OutputFormatter(true);
        $this->consoleOutput->setFormatter($outputFormatter);
        $this->consoleOutput->getFormatter()->setStyle('block', new OutputFormatterStyle('red', 'black'));
        $this->consoleOutput->getFormatter()->setStyle('dumper', new OutputFormatterStyle('blue', null, ['bold']));
        $this->consoleOutput->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue'));
        $this->outputHr = '<hr>' . str_repeat('-', 60) . '</>';
        $this->consoleOutput->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue', null));
        $maker = (isset($this->caller['class']) ? $this->caller['class'] . $this->caller['type'] : null) . $this->caller['function'] . '()';
        $this->consoleOutput->writeln(['', '<dumper>' . $maker . '</>', $this->outputHr]);
    }

    final private function handleHtmlOutput(): void
    {
        if (false === headers_sent()) {
            $this->appendHtmlOpenBody();
        }
        $this->appendStyle();
    }

    final private function appendHtmlOpenBody(): void
    {
        $this->output .= '<html style="background: ' . static::BACKGROUND_SHADE . ';"><head></head><body>';
    }

    final private function appendStyle(): void
    {
        $this->output .= '<pre style="' . static::STYLE . '">';
    }

    final private function handleClass(): void
    {
        if (isset($this->debugBacktrace[1]['class'])) {
            $class = $this->debugBacktrace[static::OFFSET]['class'];
            if (stringStartsWith('class@anonymous', $class)) {
                $class = explode('0x', $class)[0];
            }
            $this->appendClass($class, $this->debugBacktrace[static::OFFSET]['type']);
        }
    }

    final private function appendClass(string $class, string $type): void
    {
        $this->output .= $this->formatter->wrap('_class', $class) . $type;
    }

    final private function appendFunction(string $function): void
    {
        $this->output .= $this->formatter->wrap('_function', $function . '()');
    }

    final private function handleFile(): void
    {
        if (isset($this->debugBacktrace[0]['file'])) {
            $this->appendFilepath($this->debugBacktrace[0]['file'], $this->debugBacktrace[0]['line']);
        }
    }

    final private function appendFilepath(string $file, int $line): void
    {
        $this->output .= "\n" . $this->formatter->wrap('_file', Path::normalize($file) . ':' . $line);
    }

    final private function handleArgs(): void
    {
        $pos = 1;
        foreach ($this->vars as $value) {
            $this->appendArg($pos, $value);
            ++$pos;
        }
    }

    final private function appendArg(int $pos, $value): void
    {
        $varDump = $this->varDump
            ->respawn()
            ->withDump($value);
        $this->output .= 'Arg#' . $pos . ' ' . $varDump->toString() . "\n\n";
    }

    final private function handleProccessOutput(): void
    {
        if (isset($this->consoleOutput)) {
            $this->processConsoleOutput();
        } else {
            $this->processPrintOutput();
        }
    }

    final private function processConsoleOutput(): void
    {
        $this->consoleOutput->writeln($this->output, ConsoleOutput::OUTPUT_RAW);
        isset($this->outputHr) ? $this->consoleOutput->writeln($this->outputHr) : null;
    }

    final private function processPrintOutput(): void
    {
        echo $this->output;
    }
}
