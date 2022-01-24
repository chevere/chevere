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

namespace Chevere\VarDump;

use Chevere\VarDump\Interfaces\VarDumpFormatInterface;
use Chevere\VarDump\Interfaces\VarDumpInterface;
use Chevere\VarDump\Interfaces\VarDumpOutputInterface;
use function Chevere\VarSupport\deepCopy;
use Chevere\Writer\Interfaces\WriterInterface;

final class VarDump implements VarDumpInterface
{
    private array $vars = [];

    private int $shift = 0;

    private array $debugBacktrace = [];

    public function __construct(
        private VarDumpFormatInterface $format,
        private VarDumpOutputInterface $output
    ) {
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
        if ($this->vars === []) {
            return;
        }
        $this->setDebugBacktrace();
        (new VarOutput(
            $writer,
            $this->debugBacktrace,
            $this->format,
        ))
            ->process($this->output, ...$this->vars);
    }

    public function vars(): array
    {
        return deepCopy($this->vars);
    }

    public function shift(): int
    {
        return $this->shift;
    }

    /**
     * @infection-ignore-all
     */
    private function setDebugBacktrace(): void
    {
        $this->debugBacktrace = debug_backtrace();
        for ($i = 0; $i <= $this->shift; $i++) {
            array_shift($this->debugBacktrace);
        }
    }
}
