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

namespace Chevere\Components\VarDump;

use BadMethodCallException;
use Chevere\Components\Message\Message;
use Chevere\Components\VarDump\Contracts\DumperContract;
use Chevere\Components\VarDump\Formatters\DumperFormatter;
use Chevere\Components\VarDump\Contracts\FormatterContract;
use Chevere\Components\VarDump\Contracts\OutputterContract;
use Chevere\Components\VarDump\Contracts\VarDumpContract;
use Chevere\Components\VarDump\Outputters\DumperOutputter;
use function ChevereFn\stringEndsWith;

/**
 * Dumps information about one or more variables. CLI/HTML aware.
 */
class Dumper implements DumperContract
{
    private bool $isCli = false;

    private VarDump $varDump;

    private array $vars = [];

    private array $debugBacktrace;

    /**
     * Creates a new instance.
     */
    final public function __construct()
    {
        $this->varDump = new VarDump($this->getFormatter());
    }

    public function varDump(): VarDumpContract
    {
        return $this->varDump;
    }

    /**
     * {@inheritdoc}
     */
    public function withCli(): DumperContract
    {
        $new = clone $this;
        $new->isCli = true;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function isCli(): bool
    {
        return $this->isCli;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter(): FormatterContract
    {
        return new DumperFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputter(): OutputterContract
    {
        return new DumperOutputter();
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
        $this->getOutputter()
            ->withDumper($this)
            ->process();
    }

    public function vars(): array
    {
        return $this->vars;
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

    final private function handleSetDebugBacktrace(): void
    {
        $this->debugBacktrace = debug_backtrace();
        while (isset($this->debugBacktrace[0]['file']) && __FILE__ == $this->debugBacktrace[0]['file']) {
            array_shift($this->debugBacktrace);
        }
        if (
            stringEndsWith('resources/functions/dump.php', $this->debugBacktrace[0]['file'])
            && __CLASS__ == $this->debugBacktrace[0]['class']
        ) {
            array_shift($this->debugBacktrace);
        }
    }
}
