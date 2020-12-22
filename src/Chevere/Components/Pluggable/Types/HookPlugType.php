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

namespace Chevere\Components\Pluggable\Types;

use Chevere\Components\Pluggable\Plugs\Hooks\HooksQueue;
use Chevere\Interfaces\Pluggable\Plugs\Hooks\HookInterface;
use Chevere\Interfaces\Pluggable\Plugs\Hooks\PluggableHooksInterface;
use Chevere\Interfaces\Pluggable\PlugsQueueTypedInterface;
use Chevere\Interfaces\Pluggable\PlugTypeInterface;

final class HookPlugType implements PlugTypeInterface
{
    public function interface(): string
    {
        return HookInterface::class;
    }

    public function plugsTo(): string
    {
        return PluggableHooksInterface::class;
    }

    public function trailingName(): string
    {
        return 'Hook.php';
    }

    public function getPlugsQueueTyped(): PlugsQueueTypedInterface
    {
        return new HooksQueue();
    }

    public function pluggableAnchorsMethod(): string
    {
        return 'getHookAnchors';
    }
}
