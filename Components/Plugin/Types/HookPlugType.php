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

namespace Chevere\Components\Plugin\Types;

use Chevere\Components\Plugin\Interfaces\PlugTypeInterface;
use Chevere\Components\Plugs\Hooks\Interfaces\HookInterface;
use Chevere\Components\Plugs\Hooks\Interfaces\PluggableHooksInterface;

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

    public function queueName(): string
    {
        return 'Hooks';
    }

    public function pluggableAnchorsMethod(): string
    {
        return 'getHookAnchors';
    }
}
