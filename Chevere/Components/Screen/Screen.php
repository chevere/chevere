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
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use function GuzzleHttp\Psr7\stream_for;

final class Screen implements ScreenInterface
{
    private bool $traceability;

    private FormatterInterface $formatter;

    private array $trace = [];

    /** @var StreamInterface[] */
    private array $queue = [];

    private int $pos = -1;

    /**
     * Creates a new instance.
     */
    public function __construct(bool $traceability, FormatterInterface $formatter)
    {
        $this->traceability = $traceability;
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function traceability(): bool
    {
        return $this->traceability;
    }

    /**
     * {@inheritdoc}
     */
    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function trace(): array
    {
        return $this->trace;
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $display): ScreenInterface
    {
        $this->handleTrace();

        return $this->stringAdder($display);
    }

    /**
     * {@inheritdoc}
     */
    public function addNl(string $display): ScreenInterface
    {
        $this->handleTrace();

        return $this->stringAdder($display . "\n");
    }

    /**
     * {@inheritdoc}
     */
    public function addStream(StreamInterface $stream): ScreenInterface
    {
        if (!$stream->isReadable()) {
            throw new InvalidArgumentException('Stream must be readable');
        }
        $this->handleTrace();
        ++$this->pos;
        $this->queue[$this->pos] = $stream;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function queue(): array
    {
        return $this->queue;
    }

    /**
     * {@inheritdoc}
     */
    public function emit(): ScreenInterface
    {
        $this->handleTrace();
        for ($i = 0; $i <= $this->pos; $i++) {
            $stream = $this->queue[$i];
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            while (!$stream->eof()) {
                echo $stream->read(1024 * 8);
            }
            $stream->detach();
        }
        foreach ($this->queue as $stream) {
        }
        $this->queue = [];

        return $this;
    }

    private function stringAdder(string $display): ScreenInterface
    {
        ++$this->pos;
        $this->queue[$this->pos] = stream_for($this->formatter->wrap($display));

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
