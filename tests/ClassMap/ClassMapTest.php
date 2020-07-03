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
use Chevere\Exceptions\ClassMap\ClassNotExistsException;
use Chevere\Exceptions\ClassMap\ClassNotMappedException;
use Chevere\Exceptions\ClassMap\StringMappedException;
use PHPUnit\Framework\TestCase;

final class ClassMapTest extends TestCase
{
    public function testConstruct(): void
    {
        $test = 'test';
        $classMap = new ClassMap;
        $this->assertSame([], $classMap->toArray());
        $this->assertCount(0, $classMap);
        $this->assertFalse($classMap->has($test));
        $this->expectException(ClassNotMappedException::class);
        $classMap->get($test);
    }

    public function testWithPut(): void
    {
        $className = __CLASS__;
        $mapping = 'self';
        $classMap = (new ClassMap)->withPut($className, $mapping);
        $this->assertCount(1, $classMap);
        $this->assertTrue($classMap->has($className));
        $this->assertSame([$className => $mapping], $classMap->toArray());
        $this->assertSame($mapping, $classMap->get($className));
    }

    public function testWithPutSameMapping(): void
    {
        $mapping = 'self';
        $this->expectException(StringMappedException::class);
        (new ClassMap)
            ->withPut(__CLASS__, $mapping)
            ->withPut(TestCase::class, $mapping);
    }

    public function testWithPutInexistentClass(): void
    {
        $this->expectException(ClassNotExistsException::class);
        (new ClassMap)->withPut(uniqid(), 'test');
    }
}
