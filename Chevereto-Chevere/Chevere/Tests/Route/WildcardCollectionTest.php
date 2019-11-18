<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Route;

use Chevere\Components\Route\Wildcard;
use Chevere\Components\Route\WildcardCollection;
use PHPUnit\Framework\TestCase;

final class WildcardCollectionTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $this->expectNotToPerformAssertions();
        new WildcardCollection();
    }

    public function testConstruct(): void
    {
        $wildcard = new Wildcard('test');
        $wilcardCollection = new WildcardCollection($wildcard);
        $this->assertTrue($wilcardCollection->hasPos(0));
        $this->assertSame($wildcard, $wilcardCollection->getPos(0));
        $this->assertTrue($wilcardCollection->has($wildcard));
        $this->assertSame($wildcard, $wilcardCollection->get($wildcard));
        $this->assertSame([$wildcard], $wilcardCollection->toArray());
    }

    public function testWithAddedWildcard(): void
    {
        $wildcard1 = new Wildcard('test1');
        $wildcard2 = new Wildcard('test2');
        $wilcardCollection = (new WildcardCollection($wildcard1))
          ->withAddedWildcard($wildcard2);
        $this->assertTrue($wilcardCollection->hasPos(0));
        $this->assertTrue($wilcardCollection->hasPos(1));
        $this->assertTrue($wilcardCollection->has($wildcard1));
        $this->assertTrue($wilcardCollection->has($wildcard2));
        $this->assertSame($wildcard1, $wilcardCollection->get($wildcard1));
        $this->assertSame($wildcard2, $wilcardCollection->get($wildcard2));
        $this->assertSame([$wildcard1, $wildcard2], $wilcardCollection->toArray());
    }
}
