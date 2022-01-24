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

namespace Chevere\Pluggable\Interfaces\Plug\Hook;

use Chevere\Pluggable\Interfaces\PluggableAnchorsInterface;

/**
 * Describes the interface for components requiring to implement pluggable hooks.
 */
interface PluggableHooksInterface
{
    /**
     * Returns the declared hook anchors able to plug.
     */
    public static function getHookAnchors(): PluggableAnchorsInterface;

    /**
     * Return an instance with the specified `$runner`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$runner`.
     *
     * This method should be implemented in the runtime strategy before running hooks.
     */
    public function withHooksRunner(HooksRunnerInterface $runner): self;

    /**
     * Run hooks for the given anchor (if-any).
     *
     * @param string $anchor Hook anchor.
     * @param string $argument The argument to pass to hooks.
     */
    public function hook(string $anchor, &$argument): void;
}
