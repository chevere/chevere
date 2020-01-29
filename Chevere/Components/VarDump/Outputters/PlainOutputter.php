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

namespace Chevere\Components\VarDump\Outputters;

use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;
use Chevere\Components\VarDump\VarFormat;

class PlainOutputter implements OutputterInterface
{
    protected string $output = '';

    protected VarDumperInterface $varDumper;

    public function prepare(string $output): string
    {
        return $output;
    }

    public function callback(string $output): string
    {
        return $output;
    }

    final public function __construct(VarDumperInterface $varDumper)
    {
        $this->varDumper = $varDumper;
    }

    final public function varDumper(): VarDumperInterface
    {
        return $this->varDumper;
    }

    final public function toString(): string
    {
        $new = clone $this;
        $new->output = $new->prepare($new->output);
        $new->handleClass();
        $new->output .= $new->varDumper->formatter()
            ->highlight('_function', $new->varDumper->debugBacktrace()[VarDumperInterface::OFFSET]['function'] . '()');

        $new->handleFile();
        $new->output .= "\n\n";
        $new->handleArgs();
        $new->output = trim($new->output);

        return $new->callback($new->output);
    }

    final private function handleClass(): void
    {
        if (isset($this->varDumper->debugBacktrace()[VarDumperInterface::OFFSET]['class'])) {
            $class = $this->varDumper->debugBacktrace()[VarDumperInterface::OFFSET]['class'];
            $this->output .= $this->varDumper->formatter()
                    ->highlight('_class', $class) . $this->varDumper->debugBacktrace()[VarDumperInterface::OFFSET]['type'];
        }
    }

    final private function handleFile(): void
    {
        if (isset($this->varDumper->debugBacktrace()[0]['file'])) {
            $this->output .= "\n" . $this->varDumper->formatter()
                    ->highlight('_file', $this->varDumper->debugBacktrace()[0]['file'] . ':' . $this->varDumper->debugBacktrace()[0]['line']);
        }
    }

    final private function handleArgs(): void
    {
        $pos = 1;
        foreach ($this->varDumper->vars() as $value) {
            $this->appendArg($pos, $value);
            ++$pos;
        }
    }

    final private function appendArg(int $pos, $value): void
    {
        $varDump = (new VarFormat(new VarDumpeable($value), $this->varDumper->formatter()))
            ->withProcess();
        $this->output .= 'Arg#' . $pos . ' ' . $varDump->toString() . "\n\n";
    }
}
