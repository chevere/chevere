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

namespace Chevere\Tests\Dependent\Traits;

use Chevere\Components\ClassMap\ClassMap;
use Chevere\Components\Dependent\Traits\DependentTrait;
use Chevere\Exceptions\Core\TypeException;
use Chevere\Exceptions\Dependent\DependentException;
use Chevere\Interfaces\Dependent\DependentInterface;
use PHPUnit\Framework\TestCase;

final class DependentTraitTest extends TestCase
{
    public function testAssertEmpty(): void
    {
        $dependent = new class() implements DependentInterface {
            use DependentTrait;
        };
        $this->assertCount(0, $dependent->getDependencies());
        $dependent->assertDependencies();
    }

    public function testConstructMissing(): void
    {
        $this->expectException(DependentException::class);
        $this->getTestDependent();
    }

    public function testWithMissingDependency(): void
    {
        $this->expectException(DependentException::class);
        $this->getTestDependent(...[]);
    }

    public function testWithWrongDependencyType(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionCode(100);
        $this->getTestDependent(testCase: 'e');
    }

    public function testWithWrongDependencyClass(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionCode(101);
        $this->getTestDependent(testCase: new \stdClass());
    }

    public function testWithDependencyMismatch(): void
    {
        $this->expectException(TypeException::class);
        $this->expectExceptionCode(102);
        $this->getTestDependentMismatch(testCase: $this);
    }

    public function testWithDependency(): void
    {
        $dependent = $this->getTestDependent(testCase: $this);
        $this->assertObjectHasAttribute('testCase', $dependent);
        $this->assertSame($this, $dependent->testCase);
        $dependent->assertDependencies();
        $dependent->testCase = null;
        $this->expectException(DependentException::class);
        $dependent->assertDependencies();
    }

    private function getTestDependent(mixed ...$dependencies): DependentInterface
    {
        return new class(...$dependencies) implements DependentInterface {
            use DependentTrait;

            public ?TestCase $testCase;

            public function getDependencies(): ClassMap
            {
                return (new ClassMap())
                    ->withPut(TestCase::class, 'testCase');
            }
        };
    }

    private function getTestDependentMismatch(mixed ...$dependencies): DependentInterface
    {
        return new class(...$dependencies) implements DependentInterface {
            use DependentTrait;

            /**
             * $testCase won't match getDependencies
             */
            public int $testCase;

            public function getDependencies(): ClassMap
            {
                return (new ClassMap())
                    ->withPut(TestCase::class, 'testCase');
            }
        };
    }
}
