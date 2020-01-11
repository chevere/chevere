<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Dumpers;

use BadMethodCallException;
use Chevere\Components\Message\Message;
use Chevere\Components\VarDump\Contracts\DumperContract;
use Chevere\Components\VarDump\Contracts\FormatterContract;
use Chevere\Components\VarDump\Contracts\OutputterContract;
use Chevere\Components\VarDump\Contracts\VarDumpContract;
use Chevere\Components\VarDump\Dumper;
use Chevere\Components\VarDump\VarDump;

/**
 * Dumps information about one or more variables. CLI/HTML aware.
 */
abstract class AbstractDumper implements DumperContract
{
    protected FormatterContract $formatter;

    protected OutputterContract $outputter;

    protected VarDumpContract $varDump;

    protected array $vars = [];

    protected array $debugBacktrace;

    /**
     * Creates a new instance.
     */
    final public function __construct()
    {
        $this->formatter = $this->getFormatter();
        $this->outputter = $this->getOutputter();
        $this->varDump = new VarDump($this->formatter);
    }

    abstract public function getFormatter(): FormatterContract;

    abstract public function getOutputter(): OutputterContract;

    /**
     * {@inheritdoc}
     */
    public function formatter(): FormatterContract
    {
        return $this->formatter;
    }

    public function outputter(): OutputterContract
    {
        return $this->outputter;
    }

    /**
     * {@inheritdoc}
     */
    public function varDump(): VarDumpContract
    {
        return $this->varDump;
    }

    /**
     * {@inheritdoc}
     */
    final public function dump(...$vars): void
    {
        $this->vars = $vars;
        if (0 == count($this->vars)) {
            return;
        }
        $this->handleSetDebugBacktrace();
        $this->outputter
            ->withDumper($this)
            ->process();
    }

    /**
     * {@inheritdoc}
     */
    final public function vars(): array
    {
        return $this->vars;
    }

    /**
     * {@inheritdoc}
     */
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

    final private function handleSetDebugBacktrace(): void
    {
        $this->debugBacktrace = debug_backtrace();
        array_shift($this->debugBacktrace);
        if (
            isset($this->debugBacktrace[1]['class'])
            && Dumper::class == $this->debugBacktrace[1]['class']
        ) {
            array_shift($this->debugBacktrace);
            array_shift($this->debugBacktrace);
        }
        // foreach ($this->debugBacktrace as $pos => $item) {
        //     echo 'POS ' . $pos . ' ' . $item['file'] . ' -C ' . ($item['class'] ?? 'null') . ' -F ' . ($item['function'] ?? 'null') . "\n";
        // }
        // die();
    }
}
