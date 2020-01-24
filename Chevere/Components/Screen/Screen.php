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

final class Screen implements ScreenInterface
{
    /** @var StreamInterface[] The screen queue */
    private array $queue = [];

    public function attach(string $display): ScreenInterface
    {
        $this->queue[] = stream_for($display);

        return $this;
    }

    public function attachNl(string $display): ScreenInterface
    {
        $this->queue[] = stream_for($display . "\n");

        return $this;
    }

    public function queue(): array
    {
        return $this->queue;
    }

    public function display(): ScreenInterface
    {
        // TODO YIELD HERE
        foreach ($this->queue as $stream) {
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            while (!$stream->eof()) {
                echo $stream->read(1024 * 8);
            }
        }
        $this->queue = [];

        return $this;
    }
}
