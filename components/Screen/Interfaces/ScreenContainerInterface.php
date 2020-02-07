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

interface ScreenContainerInterface
{
    /**
     * Provides access to the runtime ScreenInterface instance.
     * This screen should be always "turned-on".
     */
    public function runtime(): ScreenInterface;

    /**
     * Provides access to the debug ScreenInterface instance.
     * This screen could be "turned-off" if debug is disabled. In that case, it returns a SilentScreen.
     */
    public function debug(): ScreenInterface;

    public function console(): ScreenInterface;

    public function getAll(): array;
}
