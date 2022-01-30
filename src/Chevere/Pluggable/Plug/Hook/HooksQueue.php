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

namespace Chevere\Pluggable\Plug\Hook;

use Chevere\Pluggable\Interfaces\Plug\Hook\HookInterface;
use Chevere\Pluggable\Interfaces\Plug\Hook\HooksQueueInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;
use Chevere\Pluggable\Traits\TypedPlugsQueueTrait;
use Chevere\Pluggable\Types\HookPlugType;

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
