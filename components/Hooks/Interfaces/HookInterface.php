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

namespace Chevere\Components\Hooks\Interfaces;

interface HookInterface
{
    /**
     * Returns the applicable hook anchor.
     */
    public static function anchor(): string;

    /**
     * Returns the target class name.
     */
    public static function hookableClassName(): string;

    /**
     * Returns the priority order.
     */
    public static function priority(): int;
}
