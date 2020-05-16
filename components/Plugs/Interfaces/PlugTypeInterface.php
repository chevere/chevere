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

namespace Chevere\Components\Plugs\Interfaces;

interface PlugTypeInterface
{
    /**
     * @return string The interface that the plug must implement
     */
    public function interface(): string;

    /**
     * @return string The applicable pluggable interface
     */
    public function plugsTo(): string;

    /**
     * @return string Trailing component name, like `Hooks.php` or `EventListener.php`
     */
    public function trailingName(): string;

    /**
     * @return string Name used to cache queues of this PlugType.
     */
    public function queueName(): string;

    /**
     * @return string Name of the pluggable method which returns the plug anchors
     */
    public function pluggableAnchorsMethod(): string;
}
