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

namespace Chevereto\Chevere;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

class Dumper
{
    const BACKGROUND = '#2c3e50';
    const BACKGROUND_SHADE = '#2c3e50';
    const STYLE = 'font: 16px Consolas, monospace, sans-serif; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: '.self::BACKGROUND.'; display: block; text-align: left; border: none; border-radius: 4px;';

    protected $vars;

    /** @var int */
    protected $numArgs;

    /** @var ConsoleOutputInterface */
    protected $consoleOutput;

    protected $string;

    /** @var string */
    protected $outputHr;

    protected $debugBacktrace;

    protected $caller;

    protected $callerFilepath;

    /** @var int */
    protected $offset = 1;

    public function __construct(...$vars)
    {
        $this->vars = $vars;
        $this->numArgs = func_num_args();
    }

    /**
     * Dumps information about one or more variables.
     */
    public static function dump(...$vars): void
    {
        $that = new static(...$vars);
        if ($that->numArgs == 0) {
            return;
        }
        $that->debugBacktrace = debug_backtrace();
        $that->caller = $that->debugBacktrace[0];
        $that->handleDebugBacktrace();
        $that->setCallerFilepath($that->debugBacktrace[0]['file']);
        $that->handleSelfCaller();
        $that->string = null;
        $that->handleOutput();
        $that->handleClass();
        $that->appendFunction($that->debugBacktrace[$that->offset]['function']);
        $that->handleFile();
        $that->string .= "\n\n";
        $that->handleArgs();
        $that->string = trim($that->string).'</pre>';
        $that->handleProccessOutput();
    }

    protected function handleDebugBacktrace(): void
    {
        while (isset($this->debugBacktrace[0]['file']) && $this->debugBacktrace[0]['file'] == __FILE__) {
            $this->shiftDebugBacktrace();
            $this->setCaller($this->debugBacktrace[0]);
        }
    }

    protected function setCaller(): void
    {
        $this->caller = $this->debugBacktrace[0];
    }

    protected function setCallerFilepath(string $filepath): void
    {
        $this->callerFilepath = Path::normalize($filepath);
    }

    protected function handleSelfCaller(): void
    {
        if (Utils\Str::endsWith('resources/functions/dump.php', $this->callerFilepath) && $this->debugBacktrace[0]['class'] == __CLASS__ && in_array($this->debugBacktrace[0]['function'], ['dump', 'dd'])) {
            $this->shiftDebugBacktrace();
        }
    }

    protected function shiftDebugBacktrace(): void
    {
        array_shift($this->debugBacktrace);
    }

    protected function handleOutput(): void
    {
        if (php_sapi_name() == 'cli') {
            $this->handleConsoleOutput();
        } else {
            $this->handleHtmlOutput();
        }
    }

    protected function handleConsoleOutput(): void
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

    protected function handleHtmlOutput(): void
    {
        if (false === headers_sent()) {
            $this->appendHtmlOpenBody();
        }
        $this->appendStyle();
    }

    protected function appendHtmlOpenBody(): void
    {
        $this->string .= '<html style="background: '.static::BACKGROUND_SHADE.';"><head></head><body>';
    }

    protected function appendStyle(): void
    {
        $this->string .= '<pre style="'.static::STYLE.'">';
    }

    protected function handleClass(): void
    {
        if (isset($this->debugBacktrace[1]['class'])) {
            $class = $this->debugBacktrace[$this->offset]['class'];
            if (Utils\Str::startsWith('class@anonymous', $class)) {
                $class = explode('0x', $class)[0];
            }
            $this->appendClass($class, $this->debugBacktrace[$this->offset]['type']);
        }
    }

    protected function appendClass(string $class, string $type): void
    {
        $this->string .= Utils\Dump::wrap('_class', $class).$type;
    }

    protected function appendFunction(string $function): void
    {
        $this->string .= Utils\Dump::wrap('_function', $function.'()');
    }

    protected function handleFile(): void
    {
        if (isset($this->debugBacktrace[0]['file'])) {
            $this->appendFilepath($this->debugBacktrace[0]['file'], $this->debugBacktrace[0]['line']);
        }
    }

    protected function appendFilepath(string $file, int $line): void
    {
        $this->string .= "\n".Utils\Dump::wrap('_file', Path::normalize($file).':'.$line);
    }

    protected function handleArgs(): void
    {
        $pos = 1;
        foreach ($this->vars as $value) {
            $this->appendArg($pos, $value);
            ++$pos;
        }
    }

    protected function appendArg(int $pos, $value): void
    {
        $this->string .= 'Arg#'.$pos.' '.Utils\Dump::out($value, 0)."\n\n";
    }

    protected function handleProccessOutput(): void
    {
        if (isset($this->consoleOutput)) {
            $this->processConsoleOutput();
        } else {
            $this->processPrintOutput();
        }
    }

    protected function processConsoleOutput(): void
    {
        $stripped = strip_tags($this->string);
        $this->consoleOutput->writeln($stripped, ConsoleOutput::OUTPUT_RAW);
        isset($that->outputHr) ? $this->consoleOutput->writeln($that->outputHr) : null;
    }

    protected function processPrintOutput(): void
    {
        echo $this->string;
    }

    /**
     * Dumps information about one or more variables and die().
     */
    public static function dd(...$vars)
    {
        Dumper::dump(...$vars);
        die();
    }
}
