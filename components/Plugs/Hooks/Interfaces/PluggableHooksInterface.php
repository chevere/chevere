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

namespace Chevere\Components\Plugs\Hooks\Interfaces;

use Chevere\Components\Plugin\Interfaces\PluggableAnchorsInterface;

interface PluggableHooksInterface
{
    /**
     * @return PluggableAnchorsInterface declared hook anchors.
     */
    public static function getHookAnchors(): PluggableAnchorsInterface;

    /**
     * Attach the hooks runner for this hookable.
     */
    public function withHooksRunner(HooksRunnerInterface $runner): PluggableHooksInterface;

    /**
     * Run hooks for the given anchor (if-any).
     *
     * @param string $anchor Hook anchor
     * @param string $argument An argument to pass to hooks queue
     */
    public function hook(string $anchor, &$argument): void;
}
