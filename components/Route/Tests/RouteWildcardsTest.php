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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Route\RouteWildcard;
use Chevere\Components\Route\RouteWildcards;
use PHPUnit\Framework\TestCase;

final class RouteWildcardsTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $routeWildcards = new RouteWildcards();
        $this->assertFalse($routeWildcards->hasAny());
        $this->assertSame([], $routeWildcards->toArray());
    }

    public function testConstruct(): void
    {
        $routeWildcard = new RouteWildcard('test');
        $routeWilcards = new RouteWildcards($routeWildcard);
        $this->assertTrue($routeWilcards->hasAny());
        $this->assertTrue($routeWilcards->hasPos(0));
        $this->assertSame($routeWildcard, $routeWilcards->getPos(0));
        $this->assertTrue($routeWilcards->has($routeWildcard));
        $this->assertSame($routeWildcard, $routeWilcards->get($routeWildcard));
        $this->assertSame([$routeWildcard], $routeWilcards->toArray());
    }

    public function testWithAddedWildcard(): void
    {
        $wildcards = [new RouteWildcard('test1'), new RouteWildcard('test2')];
        $routeWilcards = new RouteWildcards();
        foreach ($wildcards as $wildcard) {
            $routeWilcards = $routeWilcards
                ->withAddedWildcard($wildcard);
        }
        $this->assertTrue($routeWilcards->hasAny());
        foreach ($wildcards as $pos => $wildcard) {
            $this->assertTrue($routeWilcards->hasPos($pos));
            $this->assertTrue($routeWilcards->has($wildcard));
            $this->assertSame($wildcard, $routeWilcards->get($wildcard));
        }
        $this->assertSame($wildcards, $routeWilcards->toArray());
    }
}
