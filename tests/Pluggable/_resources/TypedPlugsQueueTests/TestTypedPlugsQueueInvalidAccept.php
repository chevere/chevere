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

namespace Chevere\Tests\Pluggable\_resources\TypedPlugsQueueTests;

use Chevere\Pluggable\Interfaces\PlugsQueueTypedInterface;
use Chevere\Pluggable\Interfaces\PlugTypeInterface;
use Chevere\Pluggable\Traits\TypedPlugsQueueTrait;
use Chevere\Pluggable\Types\HookPlugType;

final class TestTypedPlugsQueueInvalidAccept implements PlugsQueueTypedInterface
{
    use TypedPlugsQueueTrait;

    public function interface(): string
    {
        return 'SomeInvalidInterface';
    }

    public function getPlugType(): PlugTypeInterface
    {
        return new HookPlugType();
    }
}
