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
use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Message\Message;
use Chevere\Components\VarDump\Contracts\DumperContract;
use Chevere\Components\VarDump\Formatters\DumperFormatter;
use Chevere\Components\VarDump\Contracts\FormatterContract;
use Chevere\Components\VarDump\Contracts\OutputterContract;
use Chevere\Components\VarDump\Contracts\VarDumpContract;
use Chevere\Components\VarDump\Outputters\DumperOutputter;
use Chevere\Components\VarDump\VarDump;
use function ChevereFn\stringEndsWith;

/**
 * Dumps information about one or more variables. CLI/HTML aware.
 */
abstract class AbstractDumper implements DumperContract
{
    private FormatterContract $formatter;

    private OutputterContract $outputter;

    private VarDumpContract $varDump;

    private array $vars = [];

    private array $debugBacktrace;

    /**
     * Creates a new instance.
     */
    final public function __construct()
    {
        $this->formatter = $this->getFormatter();
        $this->outputter = $this->getOutputter();
        $this->varDump = new VarDump($this->formatter);
    }

    /**
     * {@inheritdoc}
     */
    public function varDump(): VarDumpContract
    {
        return $this->varDump;
    }

    public function withFormatter(FormatterContract $formatter): DumperContract
    {
        $new = clone $this;
        $new->formatter = $formatter;

        return $new;
    }

    public function formatter(): FormatterContract
    {
        return $this->formatter;
    }

    public function withOutputter(OutputterContract $outputter): DumperContract
    {
        $new = clone $this;
        $new->outputter = $outputter;

        return $new;
    }

    public function outputter(): OutputterContract
    {
        return $this->outputter;
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
        $this->outputter
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
        array_shift($this->debugBacktrace);
        $this->debugBacktrace[0]['class'] = static::class;
        if (
            stringEndsWith('resources/functions/dump.php', $this->debugBacktrace[1]['file'])
        ) {
            array_shift($this->debugBacktrace);
            array_shift($this->debugBacktrace);
        }
    }
}
