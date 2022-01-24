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

namespace Chevere\Tests\Router\Route;

use Chevere\Router\Route\RouteWildcard;
use Chevere\Router\Route\RouteWildcardMatch;
use Chevere\Router\Route\RouteWildcards;
use Chevere\Tests\src\ObjectHelper;
use FastRoute\RouteParser\Std;
use PHPUnit\Framework\TestCase;

final class RouteWildcardsTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $routeWildcards = new RouteWildcards();
        $this->assertCount(0, $routeWildcards);
    }

    public function testConstruct(): void
    {
        $wildcardName = 'test';
        $routeWildcard = new RouteWildcard(
            $wildcardName,
            new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX)
        );
        $routeWildcards = (new RouteWildcards())->withPut($routeWildcard);
        $this->assertCount(1, $routeWildcards);
        $this->assertTrue($routeWildcards->hasPos(0));
        $this->assertSame($routeWildcard, $routeWildcards->getPos(0));
        $this->assertTrue($routeWildcards->has($wildcardName));
        $this->assertSame($routeWildcard, $routeWildcards->get($wildcardName));
    }

    public function testClone(): void
    {
        $routeWildcards = new RouteWildcards();
        $clone = clone $routeWildcards;
        $this->assertNotSame($routeWildcards, $clone);
        $helper = new ObjectHelper($routeWildcards);
        $cloneHelper = new ObjectHelper($clone);
        foreach (['map', 'index'] as $property) {
            $this->assertNotSame(
                $helper->getPropertyValue($property),
                $cloneHelper->getPropertyValue($property)
            );
        }
    }

    public function testWithAddedWildcard(): void
    {
        $match = new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX);
        $wildcards = [new RouteWildcard('test1', $match), new RouteWildcard('test2', $match)];
        $routeWildcards = new RouteWildcards();
        foreach ($wildcards as $wildcard) {
            $routeWildcardsWithPut = ($routeWildcardsWithPut ?? $routeWildcards)
                ->withPut($wildcard)
                ->withPut($wildcard);
            $this->assertNotSame($routeWildcards, $routeWildcardsWithPut);
        }
        $this->assertCount(2, $routeWildcardsWithPut);
        foreach ($wildcards as $pos => $wildcard) {
            $this->assertTrue($routeWildcardsWithPut->hasPos($pos));
            $this->assertTrue($routeWildcardsWithPut->has($wildcard->__toString()));
            $this->assertEqualsCanonicalizing(
                $wildcard,
                $routeWildcardsWithPut->get($wildcard->__toString())
            );
        }
    }
}
