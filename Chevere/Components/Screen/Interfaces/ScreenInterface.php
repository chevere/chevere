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

namespace Chevere\Components\Screen\Interfaces;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

interface ScreenInterface
{
    /**
     * Provides access to the instance traceability.
     */
    public function traceability(): bool;

    /**
     * Provides access to the Formatter instance.
     */
    public function formatter(): FormatterInterface;

    /**
     * Provides access to the instance trace.
     */
    public function trace(): array;

    /**
     * Add the display to the screen queue.
     */
    public function add(string $display): ScreenInterface;

    /**
     * Add the display + new line to the screen queue.
     */
    public function addNl(string $display): ScreenInterface;

    /**
     * Add a stream to the AppendStream
     *
     * @param StreamInterface $stream Stream to append. Must be readable.
     *
     * @throws InvalidArgumentException if the stream is not readable
     */
    public function addStream(StreamInterface $stream);

    /**
     * Emit the screen queue contents.
     */
    public function emit(): ScreenInterface;

    /**
     *
     * @return array StreamInterface[]
     */
    public function queue(): array;
}
