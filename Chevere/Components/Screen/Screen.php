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

use Chevere\Components\Screen\Interfaces\FormatterInterface;
use Chevere\Components\Screen\Interfaces\ScreenInterface;
use Generator;
use GuzzleHttp\Psr7\AppendStream;
use Psr\Http\Message\StreamInterface;
use function GuzzleHttp\Psr7\stream_for;

final class Screen implements ScreenInterface
{
    private bool $traceability;

    private FormatterInterface $formatter;

    private array $trace = [];

    /** @var StreamInterface[] */
    private array $queue = [];

    public function __construct(bool $traceability, FormatterInterface $formatter)
    {
        $this->traceability = $traceability;
        $this->formatter = $formatter;
    }

    public function traceability(): bool
    {
        return $this->traceability;
    }

    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

    public function trace(): array
    {
        return $this->trace;
    }

    public function attach(string $display): ScreenInterface
    {
        $this->handleTrace();
        $this->queue[] = stream_for($this->formatter->wrap($display));

        return $this;
    }

    public function attachNl(string $display): ScreenInterface
    {
        $this->handleTrace();

        return $this->attach($display . "\n");
    }

    public function queue(): array
    {
        return $this->queue;
    }

    public function emit(): ScreenInterface
    {
        $this->handleTrace();
        foreach ($this->queue as $stream) {
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            while (!$stream->eof()) {
                echo $stream->read(1024 * 8);
            }
            $stream->detach();
        }
        $this->queue = [];

        return $this;
    }

    private function handleTrace(): void
    {
        if (!$this->traceability) {
            return;
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
}
