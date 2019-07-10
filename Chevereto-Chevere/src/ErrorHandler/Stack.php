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

namespace Chevereto\Chevere\ErrorHandler;

use const Chevereto\Chevere\CLI;
use Chevereto\Chevere\VarDumper\VarDumper;
use Chevereto\Chevere\VarDumper\Wrapper;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

/**
 * Handles the ErrorHandler exception stack trace.
 */
class Stack
{
    /** @var array */
    protected $rich;

    /** @var array */
    protected $plain;

    /** @var array */
    protected $console;

    protected $richStack;
    protected $plainStack;
    protected $consoleStack;

    /** @var array The table used to map the rich stack */
    private $richTable;

    /** @var array The table used to map the plain stack */
    private $plainTable;

    /** @var int Trace entry pointer */
    private $i;

    protected $hr = Template::BOX_BREAK_HTML;

    /**
     * @param array $trace An Exception trace
     */
    public function __construct(array $trace)
    {
        $this->i = 0;
        foreach ($trace as $entry) {
            $traceEntry = new TraceEntry($entry);
            $this->setPlainTable($traceEntry);
            if (CLI) {
                $consoleColor = new ConsoleColor();
            }
            $this->setRichTable($traceEntry, $consoleColor ?? null);
            $this->handleProcessConsole();
            $this->plain[] = strtr(Template::STACK_ITEM_HTML, $this->plainTable);
            $this->rich[] = strtr(Template::STACK_ITEM_HTML, $this->richTable);
            ++$this->i;
        }
    }

    public function getConsoleStack(): ?string
    {
        return strip_tags($this->glueString($this->console));
    }

    public function getRichStack(): ?string
    {
        return $this->wrapStringHr($this->glueString($this->rich));
    }

    public function getPlainStack(): ?string
    {
        return $this->wrapStringHr($this->glueString($this->plain));
    }

    protected function glueString(array $array)
    {
        return implode("\n".$this->hr."\n", $array);
    }

    protected function wrapStringHr(string $text): string
    {
        return $this->hr."\n".$text."\n".$this->hr;
    }

    protected function setPlainTable(TraceEntry $entry): void
    {
        $this->plainTable = [
            '%x%' => ($this->i & 1) ? 'pre--even' : null,
            '%i%' => $this->i,
            '%f%' => $entry->getArray()['file'] ?? null,
            '%l%' => $entry->getArray()['line'] ?? null,
            '%fl%' => isset($entry->getArray()['file']) ? ($entry->getArray()['file'].':'.$entry->getArray()['line']) : null,
            '%c%' => $entry->getArray()['class'] ?? null,
            '%t%' => $entry->getArray()['type'] ?? null,
            '%m%' => $entry->getArray()['function'],
            '%a%' => $entry->getPlainArgs(),
        ];
    }

    protected function setRichTable(TraceEntry $entry, ?ConsoleColor $consoleColor): void
    {
        $this->richTable = $this->plainTable;
        array_pop($this->richTable);
        // Dump types map
        foreach ([
            '%f%' => VarDumper::_FILE,
            '%l%' => VarDumper::_FILE,
            '%fl%' => VarDumper::_FILE,
            '%c%' => VarDumper::_CLASS,
            '%t%' => VarDumper::_OPERATOR,
            '%m%' => VarDumper::_FUNCTION,
        ] as $k => $v) {
            $wrapper = new Wrapper($v, (string) $this->plainTable[$k]);
            if ($consoleColor) {
                $wrapper->setCLI($consoleColor);
            }
            $this->richTable[$k] = isset($this->plainTable[$k]) ? $wrapper->toString() : null;
        }
        $this->richTable['%a%'] = $entry->getRichArgs();
    }

    protected function handleProcessConsole(): void
    {
        if (CLI) {
            $this->console[] = strtr(Template::STACK_ITEM_CONSOLE, $this->richTable);
        }
    }
}
