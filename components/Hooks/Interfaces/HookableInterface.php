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

interface HookableInterface
{
    /**
     * Returns an array with the known hook anchors
     */
    public function anchors(): array;

    /**
     * Run the hooks queue for the given anchor (if-any).
     */
    public function hook(string $anchor): void;
}
