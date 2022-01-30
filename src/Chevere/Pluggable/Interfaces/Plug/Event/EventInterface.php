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

namespace Chevere\Pluggable\Interfaces\Plug\Event;

use Chevere\Pluggable\Interfaces\PlugInterface;
use Chevere\Writer\Interfaces\WritersInterface;

/**
 * Describes the component in charge of defining an event listener plug.
 */
interface EventInterface extends PlugInterface
{
    /**
     * Executes the event listener.
     *
     * @param array $data The data passed to the event listener.
     * @param WritersInterface $writers To write to the available writing channels.
     */
    public function __invoke(array $data, WritersInterface $writers): void;
}
