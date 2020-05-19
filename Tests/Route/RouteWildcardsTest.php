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

namespace Chevere\Tests\Route;

use Chevere\Components\Route\RouteWildcard;
use Chevere\Components\Route\RouteWildcards;
use PHPUnit\Framework\TestCase;

final class RouteWildcardsTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $routeWildcards = new RouteWildcards;
        $this->assertFalse($routeWildcards->hasAny());
        $this->assertSame([], $routeWildcards->toArray());
    }

    public function testConstruct(): void
    {
        $routeWildcard = new RouteWildcard('test');
        $routeWildcards = (new RouteWildcards)->withAddedWildcard($routeWildcard);
        $this->assertTrue($routeWildcards->hasAny());
        $this->assertTrue($routeWildcards->hasPos(0));
        $this->assertSame($routeWildcard, $routeWildcards->getPos(0));
        $this->assertTrue($routeWildcards->has($routeWildcard));
        $this->assertSame($routeWildcard, $routeWildcards->get($routeWildcard));
        $this->assertSame([$routeWildcard], $routeWildcards->toArray());
    }

    public function testWithAddedWildcard(): void
    {
        $wildcards = [new RouteWildcard('test1'), new RouteWildcard('test2')];
        $routeWildcards = new RouteWildcards;
        foreach ($wildcards as $wildcard) {
            $routeWildcards = $routeWildcards
                ->withAddedWildcard($wildcard);
        }
        $this->assertTrue($routeWildcards->hasAny());
        foreach ($wildcards as $pos => $wildcard) {
            $this->assertTrue($routeWildcards->hasPos($pos));
            $this->assertTrue($routeWildcards->has($wildcard));
            $this->assertSame($wildcard, $routeWildcards->get($wildcard));
        }
        $this->assertSame($wildcards, $routeWildcards->toArray());
    }
}
