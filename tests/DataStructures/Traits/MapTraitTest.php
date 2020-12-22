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

use Chevere\Tests\DataStructures\src\UsesMapTrait;
use function DeepCopy\deep_copy;
use PHPUnit\Framework\TestCase;
use stdClass;

final class MapTraitTest extends TestCase
{
    public function testConstruct(): void
    {
        $map = new UsesMapTrait();
        $this->assertSame(0, $map->count());
        $this->assertSame([], $map->keys());
        $this->assertCount(0, $map->getGenerator());
    }

    public function testWithClone(): void
    {
        $key = 'test';
        $object = new stdClass();
        $map = new UsesMapTrait();
        $mapClone = $map->withPut($key, $object);
        $this->assertSame(1, $mapClone->count());
        $this->assertSame([$key], $mapClone->keys());
        $this->assertNotSame($map, $mapClone);
        $this->assertNotSame($map->map(), $mapClone->map());
        /**
         * @var string $string
         * @var object $value
         */
        foreach ($mapClone->getGenerator() as $string => $value) {
            $this->assertSame($key, $string);
            $this->assertSame($object, $value);
        }
        $keyAdd = 'testAdd';
        $mapClone = $mapClone->withPut($keyAdd, $object);
        $this->assertSame($object, $mapClone->map()->get($keyAdd));
        $this->assertNotSame($object, $mapClone->map()->get($key));
        $mapClone = $mapClone->withPut($keyAdd, deep_copy($object));
        $this->assertNotSame($object, $mapClone->map()->get($keyAdd));
    }
}
