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

use Chevere\Components\Hooks\HookAnchors;

interface HookableInterface
{
    /**
     * Returns the declared known hook anchors
     */
    public static function getHookAnchors(): HookAnchors;

    /**
     * Run the hooks queue for the given anchor (if-any).
     */
    public function hook(string $anchor): void;
}
