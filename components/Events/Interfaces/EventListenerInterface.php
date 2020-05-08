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

namespace Chevere\Components\Events\Interfaces;

use Chevere\Components\Plugs\Interfaces\PlugInterface;

interface EventListenerInterface extends PlugInterface
{
    /**
     * @return string Applicable event name.
     */
    public function for(): string;

    /**
     * @return string Target eventable class name.
     */
    public function at(): string;

    /**
     * @return String Priority order.
     */
    public function priority(): int;

    public function __invoke(array $data): void;
}
