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

/**
 * Handles the ErrorHandler exception stack trace.
 */
final class Stack
{
    /** @var array */
    private $rich;

    /** @var array */
    private $plain;

    /** @var array */
    private $console;

    private $hr = Template::BOX_BREAK_HTML;

    /**
     * @param array $trace An Exception trace
     */
    public function __construct(array $trace)
    {
        foreach ($trace as $k => $entry) {
            $traceEntry = new TraceEntry($entry, $k);
            if (CLI) {
                $this->console[] = strtr(Template::STACK_ITEM_CONSOLE, $traceEntry->rich());
            }
            $this->plain[] = strtr(Template::STACK_ITEM_HTML, $traceEntry->plain());
            $this->rich[] = strtr(Template::STACK_ITEM_HTML, $traceEntry->rich());
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
        return implode("\n" . $this->hr . "\n", $array);
    }

    private function wrapStringHr(string $text): string
    {
        return $this->hr . "\n" . $text . "\n" . $this->hr;
    }
}
