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

namespace Chevere\Components\Screen;

use Chevere\Components\Screen\Interfaces\ScreenInterface;
use Psr\Http\Message\StreamInterface;
use function GuzzleHttp\Psr7\stream_for;

abstract class AbstractScreen implements ScreenInterface
{
    private bool $traceability;

    private array $trace = [];

    /** @var StreamInterface[] */
    private array $queue = [];

    public function __construct(bool $traceability)
    {
        $this->traceability = $traceability;
    }

    public function traceability(): bool
    {
        return $this->traceability;
    }

    public function trace(): array
    {
        return $this->trace;
    }

    final public function attach(string $display): ScreenInterface
    {
        $this->handleTrace();
        $this->queue[] = stream_for($this->wrap($display));

        return $this;
    }

    final public function attachNl(string $display): ScreenInterface
    {
        $this->handleTrace();

        return $this->attach($display . "\n");
    }

    final public function queue(): array
    {
        return $this->queue;
    }

    final public function show(): ScreenInterface
    {
        $this->handleTrace();
        // TODO YIELD HERE
        foreach ($this->queue as $stream) {
            // if ($stream->isSeekable()) {
            //     $stream->rewind();
            // }
            // while (!$stream->eof()) {
            //     echo $stream->read(1024 * 8);
            // }
            echo $stream;
        }
        $this->queue = [];

        return $this;
    }

    private function handleTrace(): void
    {
        if (!$this->traceability) {
            // return;
        }
        $bt = debug_backtrace(0, 2);
        $caller = $bt[1];
        $fileLine = $caller['file'] . ':' . $caller['line'];
        $this->trace[] = [
            'fileLine' => $fileLine,
            'function' => $caller['function'],
            'arguments' => $caller['args'],
        ];
    }

    abstract protected function wrap(string $display): string;
}
