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

use Chevereto\Chevere\Utils\Dump;

class Stack
{
    /** @var array */
    protected $rich;

    /** @var array */
    protected $plain;

    /** @var array */
    protected $console;

    /** @var array The table used to map the rich stack */
    private $richTable;

    /** @var array The table used to map the plain stack */
    private $plainTable;

    /** @var int Trace entry pointer */
    private $i;

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

    protected function setPlainTable(TraceEntry $entry): void
    {
        $this->plainTable = [
            '%x%' => ($this->i & 1) ? 'pre--even' : null,
            '%i%' => $this->i,
            '%f%' => $entry->getArray()['file'] ?? null,
            '%l%' => $entry->getArray()['line'] ?? null,
            '%fl%' => isset($entry->getArray()['file']) ? ($entry->getArray()['file'] . ':' . $entry->getArray()['line']) : null,
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
        $this->console[] = strtr(ErrorHandler::CONSOLE_STACK_TEMPLATE, $richTable);
    }

    protected function processPlain(array $plainTable): void
    {
        $this->plain[] = strtr(ErrorHandler::HTML_STACK_TEMPLATE, $plainTable);
    }

    protected function processRich(array $richTable): void
    {
        $this->rich[] = strtr(ErrorHandler::HTML_STACK_TEMPLATE, $richTable);
    }

    public function getRich(): array
    {
        return $this->rich ?? [];
    }

    public function getPlain(): array
    {
        return $this->plain ?? [];
    }

    public function getConsole(): array
    {
        return $this->console ?? [];
    }
}
