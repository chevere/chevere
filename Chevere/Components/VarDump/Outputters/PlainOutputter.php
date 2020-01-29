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
use Chevere\Components\VarDump\Interfaces\DumperInterface;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;
use Chevere\Components\VarDump\VarFormat;
use function ChevereFn\stringStartsWith;

class PlainOutputter implements OutputterInterface
{
    protected string $output = '';

    protected DumperInterface $dumper;

    public function prepare(string $output): string
    {
        return $output;
    }

    public function callback(string $output): string
    {
        return $output;
    }

    final public function withDumper(DumperInterface $dumper): OutputterInterface
    {
        $new = clone $this;
        $new->dumper = $dumper;

        return $new;
    }

    final public function dumper(): DumperInterface
    {
        return $this->dumper;
    }

    final public function toString(): string
    {
        $this->output = $this->prepare($this->output);
        $this->handleClass();
        $this->output .= $this->dumper->formatter()
            ->highlight('_function', $this->dumper->debugBacktrace()[DumperInterface::OFFSET]['function'] . '()');

        $this->handleFile();
        $this->output .= "\n\n";
        $this->handleArgs();
        $this->output = trim($this->output);

        return $this->callback($this->output);
    }

    final private function handleClass(): void
    {
        if (isset($this->dumper->debugBacktrace()[DumperInterface::OFFSET]['class'])) {
            $class = $this->dumper->debugBacktrace()[DumperInterface::OFFSET]['class'];
            if (stringStartsWith('class@anonymous', $class)) {
                $class = explode('0x', $class)[0];
            }
            $this->output .= $this->dumper->formatter()
                    ->highlight('_class', $class) . $this->dumper->debugBacktrace()[DumperInterface::OFFSET]['type'];
        }
    }

    final private function handleFile(): void
    {
        if (isset($this->dumper->debugBacktrace()[0]['file'])) {
            $this->output .= "\n" . $this->dumper->formatter()
                    ->highlight('_file', $this->dumper->debugBacktrace()[0]['file'] . ':' . $this->dumper->debugBacktrace()[0]['line']);
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
        $varDump = (new VarFormat(new VarDumpeable($value), $this->dumper->formatter()))
            ->withProcess();
        $this->output .= 'Arg#' . $pos . ' ' . $varDump->toString() . "\n\n";
    }
}
