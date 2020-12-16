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

namespace Chevere\Tests\DataStructures\Traits;

use Chevere\Tests\DataStructures\src\UsesMapToArrayTrait;
use PHPUnit\Framework\TestCase;

final class MapToArrayTraitTest extends TestCase
{
    public function testConstruct(): void
    {
        $map = new UsesMapToArrayTrait();
        $this->assertSame([0 => 'test'], $map->toArray());
    }
}
