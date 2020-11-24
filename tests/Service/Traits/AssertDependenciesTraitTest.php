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
use Chevere\Components\Service\Traits\AssertDependenciesTrait;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\ClassMap\ClassMapInterface;
use PHPUnit\Framework\TestCase;

final class AssertDependenciesTraitTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $dependable = new class
        {
            use AssertDependenciesTrait;

            public function getDependencies(): ClassMapInterface
            {
                return new ClassMap;
            }
        };
        $this->assertCount(0, $dependable->getDependencies());
        $dependable->assertDependencies();
    }

    public function testConstruct(): void
    {
        $dependable = new class
        {
            use AssertDependenciesTrait;

            public function getDependencies(): ClassMap
            {
                return (new ClassMap)
                    ->withPut(TestCase::class, 'testCase');
            }
        };
        $this->assertCount(1, $dependable->getDependencies());
        $this->expectException(LogicException::class);
        $dependable->assertDependencies();
    }
}
