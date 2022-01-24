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

namespace Chevere\Pluggable\Interfaces;

/**
 * Describes the component in charge of defining a generic plug type.
 */
interface PlugTypeInterface
{
    /**
     * Returns the interface that the plug must implement.
     */
    public function interface(): string;

    /**
     * Returns the applicable pluggable interface.
     */
    public function plugsTo(): string;

    /**
     * Returns trailing component name, like `name.php`.
     */
    public function trailingName(): string;

    /**
     * Gets a new plugs queue typed instance.
     */
    public function getPlugsQueueTyped(): PlugsQueueTypedInterface;

    /**
     * Returns the name of the pluggable method which returns the plug anchors.
     */
    public function pluggableAnchorsMethod(): string;
}
