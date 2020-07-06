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
use Chevere\Components\Route\RouteWildcardMatch;
use Chevere\Components\Route\RouteWildcards;
use FastRoute\RouteParser\Std;
use PHPUnit\Framework\TestCase;

final class RouteWildcardsTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $routeWildcards = new RouteWildcards;
        $this->assertCount(0, $routeWildcards);
    }

    public function testConstruct(): void
    {
        $wildcardName = 'test';
        $routeWildcard = new RouteWildcard(
            $wildcardName,
            new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX)
        );
        $routeWildcards = (new RouteWildcards)->withAddedWildcard($routeWildcard);
        $this->assertCount(1, $routeWildcards);
        $this->assertTrue($routeWildcards->hasPos(0));
        $this->assertSame($routeWildcard, $routeWildcards->getPos(0));
        $this->assertTrue($routeWildcards->has($wildcardName));
        $this->assertSame($routeWildcard, $routeWildcards->get($wildcardName));
    }

    public function testWithAddedWildcard(): void
    {
        $match = new RouteWildcardMatch(Std::DEFAULT_DISPATCH_REGEX);
        $wildcards = [new RouteWildcard('test1', $match), new RouteWildcard('test2', $match)];
        $routeWildcards = new RouteWildcards;
        foreach ($wildcards as $wildcard) {
            $routeWildcards = $routeWildcards
                ->withAddedWildcard($wildcard);
        }
        $this->assertCount(2, $routeWildcards);
        foreach ($wildcards as $pos => $wildcard) {
            $this->assertTrue($routeWildcards->hasPos($pos));
            $this->assertTrue($routeWildcards->has($wildcard->name()));
            $this->assertSame($wildcard, $routeWildcards->get($wildcard->name()));
        }
    }
}
