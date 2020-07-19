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

namespace Chevere\Tests\Plugin\_resources\TypedPlugsQueueTests;

use Chevere\Components\Plugin\Traits\TypedPlugsQueueTrait;
use Chevere\Components\Plugin\Types\HookPlugType;
use Chevere\Interfaces\Plugin\PlugsQueueTypedInterface;
use Chevere\Interfaces\Plugin\PlugTypeInterface;

final class TestTypedPlugsQueueInvalidAccept implements PlugsQueueTypedInterface
{
    use TypedPlugsQueueTrait;

    public function interface(): string
    {
        return 'SomeInvalidInterface';
    }

    public function getPlugType(): PlugTypeInterface
    {
        return new HookPlugType;
    }
}
