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

namespace Chevere\Components\Plugin\Plugs\Hooks;

use Chevere\Components\Plugin\Traits\TypedPlugsQueueTrait;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Interfaces\Plugin\Plugs\Hooks\HookInterface;
use Chevere\Interfaces\Plugin\Plugs\Hooks\HooksQueueInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;

final class HooksQueue implements HooksQueueInterface
{
    use TypedPlugsQueueTrait;

    public function accept(): string
    {
        return HookInterface::class;
    }

    public function getPlugType(): PlugTypeInterface
    {
        return new HookPlugType;
    }
}
