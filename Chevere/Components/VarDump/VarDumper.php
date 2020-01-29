<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump;

use BadMethodCallException;
use Chevere\Components\Message\Message;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;

/**
 * The Chevere VarDumper.
 * Provides the actual functionality to VarDump.
 */
final class VarDumper implements VarDumperInterface
{
    protected FormatterInterface $formatter;

    protected array $vars = [];

    protected array $debugBacktrace;

    /**
     * Creates a new instance.
     */
    public function __construct(FormatterInterface $formatter, ...$vars)
    {
        $this->vars = $vars;
        $this->formatter = $formatter;
        $this->setDebugBacktrace();
    }

    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

    public function vars(): array
    {
        return $this->vars;
    }

    public function debugBacktrace(): array
    {
        return $this->debugBacktrace;
    }

    final private function setDebugBacktrace(): void
    {
        $this->debugBacktrace = debug_backtrace();
        array_shift($this->debugBacktrace);
        if (
            isset($this->debugBacktrace[1]['class'])
            && VarDump::class == $this->debugBacktrace[1]['class']
        ) {
            array_shift($this->debugBacktrace);
            array_shift($this->debugBacktrace);
        }
    }
}
