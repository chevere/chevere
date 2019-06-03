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

use Chevereto\Chevere\Dump\Dump;

/**
 * Handles the ErrorHandler Exception stack trace.
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
            $this->setRichTable($traceEntry);
            $this->handleProcessConsole();
            $this->processPlain($this->plainTable);
            $this->processRich($this->richTable);
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

    protected function setRichTable(TraceEntry $entry): void
    {
        $this->richTable = $this->plainTable;
        array_pop($this->richTable);
        // Dump types map
        foreach ([
            '%f%' => Dump::_FILE,
            '%l%' => Dump::_FILE,
            '%fl%' => Dump::_FILE,
            '%c%' => Dump::_CLASS,
            '%t%' => Dump::_OPERATOR,
            '%m%' => Dump::_FUNCTION,
        ] as $k => $v) {
            $this->richTable[$k] = isset($this->plainTable[$k]) ? Dump::wrap($v, $this->plainTable[$k]) : null;
        }
        $this->richTable['%a%'] = $entry->getRichArgs();
    }

    protected function handleProcessConsole(): void
    {
        if ('cli' == php_sapi_name()) {
            $this->processConsole($this->richTable);
        }
    }

    protected function processConsole(array $richTable): void
    {
        $this->console[] = strtr(Template::STACK_ITEM_CONSOLE, $richTable);
    }

    protected function processPlain(array $plainTable): void
    {
        $this->plain[] = strtr(Template::STACK_ITEM_HTML, $plainTable);
    }

    protected function processRich(array $richTable): void
    {
        $this->rich[] = strtr(Template::STACK_ITEM_HTML, $richTable);
    }

    public function getRich()
    {
        return $this->rich ?? [];
    }

    public function getPlain()
    {
        return $this->plain ?? [];
    }

    public function getConsole()
    {
        return $this->console ?? [];
    }
}
