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

use Chevere\Components\Route\Wildcard;
use Chevere\Components\Route\WildcardCollection;
use PHPUnit\Framework\TestCase;

final class WildcardCollectionTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $wildcardCollection = new WildcardCollection();
        $this->assertFalse($wildcardCollection->hasAny());
        $this->assertSame([], $wildcardCollection->toArray());
    }

    public function testConstruct(): void
    {
        $wildcard = new Wildcard('test');
        $wilcardCollection = new WildcardCollection($wildcard);
        $this->assertTrue($wilcardCollection->hasAny());
        $this->assertTrue($wilcardCollection->hasPos(0));
        $this->assertSame($wildcard, $wilcardCollection->getPos(0));
        $this->assertTrue($wilcardCollection->has($wildcard));
        $this->assertSame($wildcard, $wilcardCollection->get($wildcard));
        $this->assertSame([$wildcard], $wilcardCollection->toArray());
    }

    public function testWithAddedWildcard(): void
    {
        $wildcards = [new Wildcard('test1'), new Wildcard('test2')];
        $wilcardCollection = new WildcardCollection();
        foreach ($wildcards as $wildcard) {
            $wilcardCollection = $wilcardCollection
                ->withAddedWildcard($wildcard);
        }
        $this->assertTrue($wilcardCollection->hasAny());
        foreach ($wildcards as $pos => $wildcard) {
            $this->assertTrue($wilcardCollection->hasPos($pos));
            $this->assertTrue($wilcardCollection->has($wildcard));
            $this->assertSame($wildcard, $wilcardCollection->get($wildcard));
        }
        $this->assertSame($wildcards, $wilcardCollection->toArray());
    }
}
