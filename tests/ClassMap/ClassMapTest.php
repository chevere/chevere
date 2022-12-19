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

use Chevere\ClassMap\ClassMap;
use Chevere\Tests\src\ObjectHelper;
use Chevere\Throwable\Exceptions\ClassNotExistsException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
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
        $cloneHelper = new ObjectHelper($cloned);
        foreach (['map', 'flip'] as $property) {
            $this->assertNotSame(
                $helper->getPropertyValue($property),
                $cloneHelper->getPropertyValue($property),
                "Property {$property} is not cloned"
            );
        }
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
        $this->assertSame([
            $className => $key,
        ], $classMapWithPut->toArray());
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
