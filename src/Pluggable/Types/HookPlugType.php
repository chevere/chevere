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

namespace Chevere\Pluggable\Types;

use Chevere\Pluggable\Interfaces\Plug\Hook\HookInterface;
use Chevere\Pluggable\Interfaces\Plug\Hook\PluggableHooksInterface;
use Chevere\Pluggable\Interfaces\PlugsQueueTypedInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;
use Chevere\Pluggable\Plug\Hook\HooksQueue;

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
