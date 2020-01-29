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

use Chevere\Components\Screen\Interfaces\ContainerInterface;
use Chevere\Components\Screen\Interfaces\ScreenInterface;

final class Container implements ContainerInterface
{
    private array $screens = [];

    public function __construct(ScreenInterface $runtime)
    {
        $this->screens = [
            self::RUNTIME => $runtime,
            self::DEBUG => new SilentScreen,
            self::CONSOLE => new SilentScreen,
        ];
    }

    public function withDebugScreen(ScreenInterface $screen)
    {
        $new = clone $this;

        return $new->withAddedScreen(self::DEBUG, $screen);
    }

    public function withConsoleScreen(ScreenInterface $screen)
    {
        $new = clone $this;

        return $new->withAddedScreen(self::CONSOLE, $screen);
    }

    public function withAddedScreen(string $name, ScreenInterface $screen): ContainerInterface
    {
        $new = clone $this;
        $new->screens[$name] = $screen;

        return $new;
    }

    public function has(string $name): bool
    {
        return isset($this->screens[$name]);
    }

    public function get(string $name): ScreenInterface
    {
        return $this->screens[$name];
    }

    /**
     * @return array ScreenInterface[]
     */
    public function getAll(): array
    {
        return $this->screens;
    }
}
