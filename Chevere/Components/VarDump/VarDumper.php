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
use Chevere\Components\VarDump\Interfaces\DumperInterface;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;

/**
 * The Chevere VarDumper.
 * Provides the actual functionality to VarDump.
 */
class VarDumper implements DumperInterface
{
    protected FormatterInterface $formatter;

    protected OutputterInterface $outputter;

    protected array $vars = [];

    protected array $debugBacktrace;

    /**
     * Creates a new instance.
     */
    final public function __construct(FormatterInterface $formatter, OutputterInterface $outputter)
    {
        $this->formatter = $formatter;
        $this->outputter = $outputter;
    }

    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

    public function outputter(): OutputterInterface
    {
        return $this->outputter;
    }

    final public function withVars(...$vars): DumperInterface
    {
        $new = clone $this;
        $new->vars = $vars;
        if (0 == count($new->vars)) {
            return $new;
        }
        $new->setDebugBacktrace();

        return $new;
    }

    final public function vars(): array
    {
        return $this->vars;
    }

    final public function toString(): string
    {
        return $this->outputter
            ->withDumper($this)
            ->toString();
    }

    public function debugBacktrace(): array
    {
        if (!isset($this->debugBacktrace)) {
            throw new BadMethodCallException(
                (new Message('Method %callMethodName% must not be called before calling the %methodName%'))
                    ->code('%callMethodName%', __METHOD__)
                    ->code('%methodName%', __CLASS__ . '::dump')
                    ->toString()
            );
        }

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
