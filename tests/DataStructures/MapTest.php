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

namespace Chevere\Tests\DataStructures;

use Chevere\Components\DataStructures\Map;
use Chevere\Exceptions\Core\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

use function DeepCopy\deep_copy;

final class MapTest extends TestCase
{
    public function testAssertEmpty(): void
    {
        $map = new Map([]);
        $this->expectException(OutOfBoundsException::class);
        $map->assertHasKey('not-found');
    }

    public function testGetEmpty(): void
    {
        $map = new Map([]);
        $this->expectException(OutOfBoundsException::class);
        $map->get('not-found');
    }

    public function testWithPut(): void
    {
        $key = 'key';
        $value = 1234;
        $map = new Map([]);
        $mapCloned = $map->withPut($key, $value);
        $this->assertNotSame($map, $mapCloned);
        $this->assertNotEquals($map->keys(), $mapCloned->keys());
        $this->assertSame($value, $mapCloned->get($key));
        $mapCloned->assertHasKey($key);
    }
}
