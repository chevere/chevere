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

namespace Chevere\ErrorHandler\src;

use const Chevere\CLI;
use Chevere\VarDump\VarDump;

/**
 * Handles the ErrorHandler exception stack trace.
 */
// FIXME: One "stack" for each $rich, $plain and $console. Share the same interface.
final class Stack
{
    /** @var array */
    private $rich;

    /** @var array */
    private $plain;

    /** @var array */
    private $console;

    /** @var array The table used to map the rich stack */
    private $richTable;

    /** @var array The table used to map the plain stack */
    private $plainTable;

    /** @var int Trace entry pointer */
    private $i;

    private $hr = Template::BOX_BREAK_HTML;

    /**
     * @param array $trace An Exception trace
     */
    public function __construct(array $trace)
    {
        $this->i = 0;
        foreach ($trace as $entry) {
            $traceEntry = new TraceEntry($entry);
            $this->setPlainTable($traceEntry);
            $this->setRichTable($traceEntry);
            $this->handleProcessConsole();
            $this->plain[] = strtr(Template::STACK_ITEM_HTML, $this->plainTable);
            $this->rich[] = strtr(Template::STACK_ITEM_HTML, $this->richTable);
            ++$this->i;
        }
    }

    public function getConsoleStack(): ?string
    {
        return strip_tags($this->wrapStringHr($this->glueString($this->console)));
    }

    public function getRichStack(): ?string
    {
        return $this->wrapStringHr($this->glueString($this->rich));
    }

    public function getPlainStack(): ?string
    {
        return $this->wrapStringHr($this->glueString($this->plain));
    }

    private function glueString(array $array)
    {
        return implode("\n".$this->hr."\n", $array);
    }

    private function wrapStringHr(string $text): string
    {
        return $this->hr."\n".$text."\n".$this->hr;
    }

    private function setPlainTable(TraceEntry $entry): void
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

    private function setRichTable(TraceEntry $entry): void
    {
        $this->richTable = $this->plainTable;
        array_pop($this->richTable);
        // Dump types map
        foreach ([
            '%f%' => VarDump::_FILE,
            '%l%' => VarDump::_FILE,
            '%fl%' => VarDump::_FILE,
            '%c%' => VarDump::_CLASS,
            '%t%' => VarDump::_OPERATOR,
            '%m%' => VarDump::_FUNCTION,
        ] as $k => $v) {
            $wrapper = VarDump::wrap($v, (string) $this->plainTable[$k]);
            $this->richTable[$k] = isset($this->plainTable[$k]) ? $wrapper : null;
        }
        $this->richTable['%a%'] = $entry->getRichArgs();
    }

    private function handleProcessConsole(): void
    {
        if (CLI) {
            $this->console[] = strtr(Template::STACK_ITEM_CONSOLE, $this->richTable);
        }
    }
}
