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
use Chevere\Components\Hooks\Hooks;
use Chevere\Components\Hooks\HooksQueue;

interface HookableInterface
{
    /**
     * Returns the declared hook anchors
     */
    public static function getHookAnchors(): HookAnchors;

    /**
     * Attach the hooks queue for this hookable.
     */
    public function withHooksQueue(HooksQueue $hooksQueue): HookableInterface;

    /**
     * Run the hooks queue for the given anchor (if-any).
     */
    public function hook(string $anchor, &$argument): void;
}
