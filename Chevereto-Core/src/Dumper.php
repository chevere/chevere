<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Dumper
{
    const BACKGROUND = '#2c3e50';
    const BACKGROUND_SHADE = '#2c3e50';
    const STYLE = 'font: 16px Consolas, monospace, sans-serif; color: #ecf0f1; padding: 15px; margin: 10px 0; word-break: break-word; white-space: pre-wrap; background: ' . self::BACKGROUND . '; display: block; text-align: left; border: none; border-radius: 4px;';
    /**
     * Dumps information about one or more variables.
     */
    public static function dump(...$vars) : void
    {
        $numArgs = func_num_args();
        if ($numArgs == 0) {
            return;
        }
        $trace = debug_backtrace();
        $caller = $trace[0];
        // Avoid being self dumped, use VarDumper::dump to dump here.
        while (isset($trace[0]['file']) && $trace[0]['file'] == __FILE__) {
            array_shift($trace);
            $caller = $trace[0];
        }
        $maker = (isset($caller['class']) ? $caller['class'] . $caller['type'] : null) . $caller['function'] . '()';
        $dump = null;
        if (php_sapi_name() == 'cli') {
            $consoleColor = new ConsoleColor();
            $output = new ConsoleOutput();
            $outputFormatter = new OutputFormatter(true);
            $output->setFormatter($outputFormatter);
            $output->getFormatter()->setStyle('block', new OutputFormatterStyle('red', 'black'));
            $output->getFormatter()->setStyle('dumper', new OutputFormatterStyle('blue', null, ['bold']));
            $output->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue'));
            $outputHr = '<hr>' . str_repeat('-', 60) . '</>';
            $output->getFormatter()->setStyle('hr', new OutputFormatterStyle('blue', null));
            // $formatter = new FormatterHelper();
            // $formattedBlock = $formatter->formatBlock($maker, 'dumper', true);
            // $output->writeln([$outputHr, $formattedBlock, $outputHr]);
            $output->writeln(['', '<dumper>' . $maker . '</>', $outputHr]);
        } else {
            if (headers_sent() == false) {
                $dump .= '<html style="background: ' . static::BACKGROUND_SHADE . ';"><head></head><body>';
            }
            $dump .= '<pre style="' . static::STYLE . '">';
        }
        $offset = 1;
        if (isset($trace[1]) && isset($trace[1]['class'])) {
            $class = $trace[$offset]['class'];
            if (Utils\Str::startsWith('class@anonymous', $class)) {
                $class = explode('0x', $class)[0];
            }
            $dump .= Utils\Dump::wrap('_class', $class) . $trace[$offset]['type'];
        }
        $dump .= Utils\Dump::wrap('_function', $trace[$offset]['function'] . '()');
        if (isset($trace[0]['file'])) {
            $dump .= "\n". Utils\Dump::wrap('_file', Path::normalize($trace[0]['file'])) . Utils\Dump::wrap('_operator', ':' . $trace[0]['line']);
        }
        $dump .=  "\n\n";
        $i = 1;
        foreach ($vars as $k => $value) {
            if ($numArgs > 1) {
                $dump .= 'Arg#' . $i . ' ';
            }
            $val = Utils\Dump::out($value, 0);
            $dump .= $val . "\n\n";
            $i++;
        }
        $dump = trim($dump) . '</pre>';
        if (isset($output)) {
            $dumpConsole = strip_tags($dump);
            $output->writeln($dumpConsole, ConsoleOutput::OUTPUT_RAW);
            isset($outputHr) ? $output->writeln($outputHr) : null;
        } else {
            echo $dump;
        }
    }
    /**
     * Dumps information about one or more variables and die().
     */
    public static function dd(...$vars)
    {
        Dumper::dump(...$vars);
        die(1);
    }
}
/**
 * Dumps information about one or more variables.
 */
function dump(...$vars)
{
    Dumper::dump(...$vars);
}
/**
 * Dumps information about one or more variables and die().
 */
function dd(...$vars)
{
    Dumper::dd(...$vars);
}
