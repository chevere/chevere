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

use Chevere\Action\Action;
use Chevere\Dependent\Dependencies;
use Chevere\Dependent\Exceptions\MissingDependenciesException;
use Chevere\Dependent\Interfaces\DependenciesInterface;
use Chevere\Dependent\Interfaces\DependentInterface;
use Chevere\Dependent\Traits\DependentTrait;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Throwable\Errors\TypeError;
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
        $this->expectException(MissingDependenciesException::class);
        $this->getTestDependent();
    }

    public function testWithMissingDependency(): void
    {
        $this->expectException(MissingDependenciesException::class);
        $this->getTestDependent(...[]);
    }

    public function testWithWrongDependencyClass(): void
    {
        $this->expectException(TypeError::class);
        $this->getTestDependent(testCase: new \stdClass());
    }

    public function testWithDependencyMismatch(): void
    {
        $this->expectException(TypeError::class);
        $this->getTestDependentMismatch(testCase: $this);
    }

    public function testWithParentConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        $this->getTestDependentParentConstructor(testCase: $this);
    }

    public function testWithDependency(): void
    {
        $property = 'testCase';
        $dependent = $this->getTestDependent(...[
            $property => $this,
        ]);
        $this->assertObjectHasAttribute($property, $dependent);
        $this->assertSame($this, $dependent->testCase);
        $this->assertCount(1, $dependent->dependencies());
        $this->assertSame([$property], $dependent->dependencies()->keys());
        $dependent->assertDependencies();
    }

    public function testAssertDependencies(): void
    {
        $dependent = $this->getTestDependent(testCase: $this);
        $dependent->assertDependencies();
        $dependent->testCase = null;
        $this->expectException(MissingDependenciesException::class);
        $dependent->assertDependencies();
    }

    private function getTestDependent(mixed ...$dependencies): DependentInterface
    {
        $class = new class() implements DependentInterface {
            use DependentTrait;

            public ?TestCase $testCase;

            public function getDependencies(): DependenciesInterface
            {
                return new Dependencies(testCase: TestCase::class);
            }
        };

        return $class->withDependencies(...$dependencies);
    }

    private function getTestDependentMismatch(mixed ...$dependencies): DependentInterface
    {
        $class = new class() implements DependentInterface {
            use DependentTrait;

            /**
             * $testCase won't match getDependencies
             */
            public int $testCase;

            public function getDependencies(): DependenciesInterface
            {
                return new Dependencies(testCase: TestCase::class);
            }
        };

        return $class->withDependencies(...$dependencies);
    }

    private function getTestDependentParentConstructor(mixed ...$dependencies): DependentInterface
    {
        $class = new class() extends Action implements DependentInterface {
            use DependentTrait;

            public testCase $testCase;

            public function getDependencies(): DependenciesInterface
            {
                return new Dependencies(testCase: TestCase::class);
            }

            public function run(ArgumentsInterface $arguments): ResponseInterface
            {
                return $this->getResponse();
            }
        };

        return $class->withDependencies(...$dependencies);
    }
}
