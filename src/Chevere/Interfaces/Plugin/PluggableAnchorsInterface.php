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

namespace Chevere\Interfaces\Plugin;

use Chevere\Exceptions\Core\OverflowException;
use Ds\Set;

/**
 * Describes the component in charge of defining pluggable anchors.
 */
interface PluggableAnchorsInterface
{
    /**
     * Return an instance with the specified added `$anchor`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added `$anchor`.
     *
     * @throws OverflowException
     */
    public function withAdded(string $anchor): PluggableAnchorsInterface;

    /**
     * Indicates whether the instance has the given `$anchor`.
     */
    public function has(string $anchor): bool;

    /**
     * Provides access to a cloned set instance.
     */
    public function clonedSet(): Set;
}
