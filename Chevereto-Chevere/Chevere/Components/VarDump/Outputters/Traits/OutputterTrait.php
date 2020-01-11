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

namespace Chevere\Components\VarDump\Outputters\Traits;

use Chevere\Components\VarDump\Contracts\DumperContract;
use Chevere\Components\VarDump\Contracts\OutputterContract;
use Chevere\Components\VarDump\VarDump;
use function ChevereFn\stringStartsWith;

trait OutputterTrait
{
    protected string $output = '';

    protected DumperContract $dumper;

    /**
     * {@inheritdoc}
     */
    public function prepare(): OutputterContract
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function printOutput(): void
    {
        echo $this->output;
    }

    /**
     * {@inheritdoc}
     */
    final public function withDumper(DumperContract $dumper): OutputterContract
    {
        $new = clone $this;
        $new->dumper = $dumper;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    final public function dumper(): DumperContract
    {
        return $this->dumper;
    }

    /**
     * {@inheritdoc}
     */
    final public function process(): OutputterContract
    {
        $this->prepare();
        $this->handleClass();
        $this->output .= $this->dumper->varDump()->formatter()
            ->applyWrap('_function', $this->dumper->debugBacktrace()[$this->dumper::OFFSET]['function'] . '()');
        $this->handleFile();
        $this->output .= "\n\n";
        $this->handleArgs();
        $this->output = trim($this->output);
        $this->printOutput();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function toString(): string
    {
        return $this->output;
    }

    final private function handleClass(): void
    {
        if (isset($this->dumper->debugBacktrace()[1]['class'])) {
            $class = $this->dumper->debugBacktrace()[$this->dumper::OFFSET]['class'];
            if (stringStartsWith('class@anonymous', $class)) {
                $class = explode('0x', $class)[0];
            }
            $this->output .= $this->dumper->varDump()->formatter()
                ->applyWrap('_class', $class) . $this->dumper->debugBacktrace()[$this->dumper::OFFSET]['type'];
        }
    }

    final private function handleFile(): void
    {
        if (isset($this->dumper->debugBacktrace()[0]['file'])) {
            $this->output .= "\n" . $this->dumper->varDump()->formatter()
                ->applyWrap('_file', $this->dumper->debugBacktrace()[0]['file'] . ':' . $this->dumper->debugBacktrace()[0]['line']);
        }
    }

    final private function handleArgs(): void
    {
        $pos = 1;
        foreach ($this->dumper->vars() as $value) {
            $this->appendArg($pos, $value);
            ++$pos;
        }
    }

    final private function appendArg(int $pos, $value): void
    {
        $varDump = (new VarDump($this->dumper->varDump()->formatter()))
            ->withDontDump(...$this->dumper->varDump()->dontDump())
            ->withVar($value)
            ->process();
        $this->output .= 'Arg#' . $pos . ' ' . $varDump->toString() . "\n\n";
    }
}
