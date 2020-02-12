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
use Chevere\Components\Common\Interfaces\ToStringInterface;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;
use Chevere\Components\Writers\Interfaces\StreamWriterInterface;
use Chevere\Components\Writers\Interfaces\WritersInterface;
use Chevere\Components\Writers\Writers;

/**
 * The Chevere VarDump.
 * A context-aware VarDumper.
 *
 * @codeCoverageIgnore
 */
final class VarDump
{
    private $vars;

    private StreamWriterInterface $streamWriter;

    private int $shift = 0;

    public function __construct(StreamWriterInterface $streamWriter, ...$vars)
    {
        $this->streamWriter = $streamWriter;
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
            $formatter = HtmlOutputter::class;
            $outputter = HtmlFormatter::class;
        }
        $dumper = (new VarDumper(
            $this->debugBacktrace,
            new $formatter,
            ...$this->vars
        ));
        (new $outputter(
            $dumper,
            $this->streamWriter
        ))->emit();
    }

    final private function setDebugBacktrace(): void
    {
        $this->debugBacktrace = debug_backtrace();
        for ($i = 0; $i <= $this->shift; $i++) {
            array_shift($this->debugBacktrace);
        }
        while (
            isset($this->debugBacktrace[1]['class'])
            && VarDump::class == $this->debugBacktrace[1]['class']
        ) {
            // @codeCoverageIgnoreStart
            array_shift($this->debugBacktrace);
            // @codeCoverageIgnoreEnd
        }
        while (
            isset($this->debugBacktrace[1]['function'])
            && in_array($this->debugBacktrace[1]['function'], ['xdump', 'xdd'])
        ) {
            // @codeCoverageIgnoreStart
            array_shift($this->debugBacktrace);
            // @codeCoverageIgnoreEnd
        }
    }
}
