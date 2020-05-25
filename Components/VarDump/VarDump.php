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

use Chevere\Interfaces\VarDump\FormatterInterface;
use Chevere\Interfaces\VarDump\OutputterInterface;
use Chevere\Interfaces\VarDump\VarDumpInterface;
use Chevere\Interfaces\Writers\WriterInterface;

final class VarDump implements VarDumpInterface
{
    private array $vars = [];

    private WriterInterface $writer;

    private FormatterInterface $formatter;

    private OutputterInterface $outputter;

    private int $shift = 0;

    private array $debugBacktrace = [];

    public function __construct(
        WriterInterface $writer,
        FormatterInterface $formatter,
        OutputterInterface $outputter
    ) {
        $this->writer = $writer;
        $this->formatter = $formatter;
        $this->outputter = $outputter;
    }

    public function withVars(...$vars): VarDumpInterface
    {
        $new = clone $this;
        $new->vars = $vars;

        return $new;
    }

    public function withShift(int $shift): VarDumpInterface
    {
        $new = clone $this;
        $new->shift = $shift;

        return $new;
    }

    public function stream(): void
    {
        if (empty($this->vars)) {
            return;
        }
        $this->setDebugBacktrace();
        (new VarOutputter(
            $this->writer,
            $this->debugBacktrace,
            $this->formatter,
            ...$this->vars
        ))
            ->process($this->outputter);
    }

    final private function setDebugBacktrace(): void
    {
        // 0: helper or maker (like xdd), 1: where 0 got called
        $this->debugBacktrace = debug_backtrace();
        for ($i = 0; $i <= $this->shift; $i++) {
            array_shift($this->debugBacktrace);
        }
    }
}
