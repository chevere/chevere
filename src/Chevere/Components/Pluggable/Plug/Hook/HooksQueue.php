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

namespace Chevere\Components\Pluggable\Plug\Hook;

use Chevere\Components\Pluggable\Traits\TypedPlugsQueueTrait;
use Chevere\Components\Pluggable\Types\HookPlugType;
use Chevere\Interfaces\Pluggable\Plug\Hook\HookInterface;
use Chevere\Interfaces\Pluggable\Plug\Hook\HooksQueueInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;

final class HooksQueue implements HooksQueueInterface
{
    use TypedPlugsQueueTrait;

    public function interface(): string
    {
        return HookInterface::class;
    }

    public function getPlugType(): PlugTypeInterface
    {
        return new HookPlugType();
    }
}
