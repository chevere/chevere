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

use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;
use Chevere\Components\Writers\Interfaces\WriterInterface;

/**
 * The Chevere VarDump.
 * A context-aware VarDumper.
 *
 * @codeCoverageIgnore
 */
final class VarDump
{
    private $vars;

    private WriterInterface $writer;

    private int $shift = 0;

    private array $debugBacktrace = [];

    public function __construct(WriterInterface $writer, ...$vars)
    {
        $this->writer = $writer;
        $this->vars = $vars;
    }

    // Set the shift int, it will be used to remove self-related traces
    public function withShift(int $shift): VarDump
    {
        $new = clone $this;
        $new->shift = $shift;

        return $new;
    }

    public function shift(): int
    {
        return $this->shift;
    }

    public function stream(): void
    {
        $this->setDebugBacktrace();
        if (BootstrapInstance::get()->isCli()) {
            $formatter = ConsoleFormatter::class;
            $outputter = ConsoleOutputter::class;
        } else {
            $outputter = HtmlOutputter::class;
            $formatter = HtmlFormatter::class;
        }
        (new VarOutputter(
            $this->writer,
            $this->debugBacktrace,
            new $formatter,
            ...$this->vars
        ))->process(new $outputter);
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
