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

use Chevere\Interfaces\Plugin\TypedPlugsQueueInterface;
use Chevere\Components\Plugin\Traits\TypedPlugsQueueTrait;

final class TestTypedPlugsQueueInvalidAccept implements TypedPlugsQueueInterface
{
    use TypedPlugsQueueTrait;

    public function accept(): string
    {
        return 'SomeInvalidInterface';
    }
}
