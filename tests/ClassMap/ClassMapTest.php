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

namespace Chevere\Tests\ClassMap;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Exceptions\Core\ClassNotExistsException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Tests\src\ObjectHelper;
use PHPUnit\Framework\TestCase;

final class ClassMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $test = 'test';
        $classMap = new ClassMap();
        $this->assertCount(0, $classMap);
        $this->assertFalse($classMap->has($test));
        $this->expectException(OutOfBoundsException::class);
        $classMap->key($test);
    }

    public function testClone(): void
    {
        $classMap = new ClassMap();
        $cloned = clone $classMap;
        $helper = new ObjectHelper($classMap);
        $map = $helper->getPropertyValue('map');
        $flip = $helper->getPropertyValue('flip');
        $cloneHelper = new ObjectHelper($cloned);
        $mapCloned = $cloneHelper->getPropertyValue('map');
        $flipCloned = $cloneHelper->getPropertyValue('flip');
        $this->assertNotSame($map, $mapCloned);
        $this->assertNotSame($flip, $flipCloned);
    }

    public function testEmptyClassName(): void
    {
        $key = 'test';
        $classMap = new ClassMap();
        $this->expectException(OutOfBoundsException::class);
        $classMap->className($key);
    }

    public function testWithPut(): void
    {
        $className = self::class;
        $key = 'self';
        $classMap = new ClassMap();
        $classMapWithPut = $classMap->withPut($className, $key);
        $this->assertNotSame($classMap, $classMapWithPut);
        $this->assertCount(1, $classMapWithPut);
        $this->assertTrue($classMapWithPut->has($className));
        $this->assertTrue($classMapWithPut->hasKey($key));
        $this->assertSame([$className => $key], $classMapWithPut->toArray());
        $this->assertSame($key, $classMapWithPut->key($className));
        $this->assertSame($className, $classMapWithPut->className($key));
        $this->assertSame([$key], $classMapWithPut->keys());
    }

    public function testWithPutOverflow(): void
    {
        $mapping = 'self';
        $this->expectException(OverflowException::class);
        (new ClassMap())
            ->withPut(self::class, $mapping)
            ->withPut(TestCase::class, $mapping);
    }

    public function testWithPutClassNotExists(): void
    {
        $this->expectException(ClassNotExistsException::class);
        (new ClassMap())->withPut(uniqid(), 'test');
    }
}
