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

namespace Chevere\Tests\Service\Traits;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Service\Traits\ServiceDependantTrait;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Interfaces\Service\ServiceDependantInterface;
use PHPUnit\Framework\TestCase;

final class ServiceDependantTraitTest extends TestCase
{
    private function getTestDependant(): ServiceDependantInterface
    {
        return new class() implements ServiceDependantInterface {
            use ServiceDependantTrait;

            public TestCase $testCase;

            public function getDependencies(): ClassMap
            {
                return (new ClassMap())
                    ->withPut(TestCase::class, 'testCase');
            }
        };
    }

    private function getTestDependantMismatch(): ServiceDependantInterface
    {
        return new class() implements ServiceDependantInterface {
            use ServiceDependantTrait;

            /** $testCase won't match getDependencies */
            public int $testCase;

            public function getDependencies(): ClassMap
            {
                return (new ClassMap())
                    ->withPut(TestCase::class, 'testCase');
            }
        };
    }

    public function testAssertEmpty(): void
    {
        $dependable = new class() implements ServiceDependantInterface {
            use ServiceDependantTrait;
        };
        $this->assertCount(0, $dependable->getDependencies());
        $dependable->assertDependencies();
    }

    public function testAssertMissing(): void
    {
        $dependable = $this->getTestDependant();
        $this->assertCount(1, $dependable->getDependencies());
        $this->expectException(InvalidArgumentException::class);
        $dependable->assertDependencies();
    }

    public function testWithMissingDependency(): void
    {
        $dependable = $this->getTestDependant();
        $this->expectException(InvalidArgumentException::class);
        $dependable->withDependencies(...[]);
    }

    public function testWithWrongDependencyType(): void
    {
        $dependable = $this->getTestDependant();
        $this->expectException(TypeException::class);
        $this->expectExceptionCode(100);
        $dependable->withDependencies(testCase: 'e');
    }

    public function testWithWrongDependencyClass(): void
    {
        $dependable = $this->getTestDependant();
        $this->expectException(TypeException::class);
        $this->expectExceptionCode(101);
        $dependable->withDependencies(testCase: new \stdClass());
    }

    public function testWithDependencyMismatch(): void
    {
        $dependable = $this->getTestDependantMismatch();
        $this->expectException(TypeException::class);
        $this->expectExceptionCode(102);
        $dependable->withDependencies(testCase: $this);
    }

    public function testWithDependency(): void
    {
        $property = 'testCase';
        $dependable = $this->getTestDependant();
        $dependable = $dependable->withDependencies(...[$property => $this]);
        $this->assertObjectHasAttribute($property, $dependable);
        $this->assertSame($this, $dependable->testCase);
    }
}
