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
use Chevere\Interfaces\Writer\WriterInterface;
use function DeepCopy\deep_copy;

final class VarDump implements VarDumpInterface
{
    private array $vars = [];

    private FormatterInterface $formatter;

    private OutputterInterface $outputter;

    private int $shift = 0;

    private array $debugBacktrace = [];

    public function __construct(
        FormatterInterface $formatter,
        OutputterInterface $outputter
    ) {
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

    public function process(WriterInterface $writer): void
    {
        if (empty($this->vars)) {
            return;
        }
        $this->setDebugBacktrace();
        (new VarOutputter(
            $writer,
            $this->debugBacktrace,
            $this->formatter,
            ...$this->vars
        ))
            ->process($this->outputter);
    }

    public function vars(): array
    {
        return deep_copy($this->vars);
    }

    public function shift(): int
    {
        return $this->shift;
    }

    private function setDebugBacktrace(): void
    {
        // 0: helper or maker (like xdd), 1: where 0 got called
        $this->debugBacktrace = debug_backtrace();
        for ($i = 0; $i <= $this->shift; $i++) {
            array_shift($this->debugBacktrace);
        }
    }
}
