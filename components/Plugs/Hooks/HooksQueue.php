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

namespace Chevere\Components\Plugs\Hooks;

use Chevere\Components\Plugin\Traits\TypedPlugsQueueTrait;
use Chevere\Interfaces\Plugs\Hooks\HookInterface;
use Chevere\Interfaces\Plugs\Hooks\HooksQueueInterface;

final class HooksQueue implements HooksQueueInterface
{
    use TypedPlugsQueueTrait;

    public function accept(): string
    {
        return HookInterface::class;
    }
}
