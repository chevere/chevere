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

/**
 * A completely silent screen.
 */
final class SilentScreen implements ScreenInterface
{
    /** @var StreamInterface[] The screen queue */
    private array $queue = [];

    public function attach(string $display): ScreenInterface
    {
        return $this;
    }

    public function attachNl(string $display): ScreenInterface
    {
        return $this;
    }

    public function queue(): array
    {
        return $this->queue;
    }

    public function display(): ScreenInterface
    {
        return $this;
    }
}
