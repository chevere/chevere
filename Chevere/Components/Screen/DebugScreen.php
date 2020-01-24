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
 * A debug screen.
 */
final class DebugScreen implements ScreenInterface
{
    private ScreenInterface $screen;

    public function __construct()
    {
        $this->screen = new Screen;
    }

    public function attach(string $display): ScreenInterface
    {
        $this->screen
            ->attach($this->debugWrap($display));

        return $this;
    }

    public function attachNl(string $display): ScreenInterface
    {
        $this->screen
            ->attachNl($this->debugWrap($display));

        return $this;
    }

    public function queue(): array
    {
        return $this->screen->queue();
    }

    public function display(): ScreenInterface
    {
        $this->screen->display();

        return $this;
    }

    private function debugWrap(string $display): string
    {
        $debug_backtrace = debug_backtrace(0, 1)[0];
        xdd($debug_backtrace);

        return '';
    }
}
