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

use Psr\Http\Message\StreamInterface;

interface ScreenInterface
{
    public function traceability(): bool;

    public function formatter(): FormatterInterface;

    public function trace(): array;

    /**
     * Attach the display to the screen queue.
     */
    public function attach(string $display): ScreenInterface;

    /**
     * Attach the display + new line to the screen queue.
     */
    public function attachNl(string $display): ScreenInterface;

    /**
     * Provides access to the screen queue.
     *
     * @return StreamInterface[]
     */
    public function queue(): array;

    /**
     * Show the screen queue contents.
     */
    public function show(): ScreenInterface;
}
