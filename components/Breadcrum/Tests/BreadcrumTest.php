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

namespace Chevere\Components\Breadcrum\Tests;

use Chevere\Components\Breadcrum\Breadcrum;
use Chevere\Components\Breadcrum\Exceptions\BreadcrumException;
use PHPUnit\Framework\TestCase;

final class BreadcrumTest extends TestCase
{
    private function getiItems(): array
    {
        return [
            'test-0',
            'test-1',
            'test-2',
        ];
    }

    public function testConstruct(): void
    {
        $breadcrum = new Breadcrum();
        $this->assertEmpty($breadcrum->toArray());
        $this->assertEmpty($breadcrum->toString());
        $this->assertFalse($breadcrum->hasAny());
        $this->assertFalse($breadcrum->has(0));
        $this->assertSame(-1, $breadcrum->pos());
    }

    public function testWithAddedItems(): void
    {
        $items = $this->getiItems();
        $breadcrum = new Breadcrum();
        foreach ($items as $pos => $item) {
            $breadcrum = $breadcrum
                ->withAddedItem($item);
            $this->assertTrue($breadcrum->has($pos));
            $this->assertSame($pos, $breadcrum->pos());
            $this->assertContains($item, $breadcrum->toArray());
            $this->assertStringContainsString($item, $breadcrum->toString());
        }
        $this->assertSame($items, $breadcrum->toArray());
        $breadcrum = $breadcrum
            ->withRemovedItem(1);
        $this->assertNotContains($items[1], $breadcrum->toArray());
        $this->assertStringNotContainsString($items[1], $breadcrum->toString());
    }

    public function testWithRemovedItems(): void
    {
        $items = $this->getiItems();
        $breadcrum = new Breadcrum();
        $pos = 0;
        foreach ($items as $pos => $item) {
            $breadcrum = $breadcrum
                ->withAddedItem($item)
                ->withRemovedItem($pos);
            $this->assertFalse($breadcrum->has($pos));
            $this->assertNotContains($item, $breadcrum->toArray());
            $this->assertStringNotContainsString($item, $breadcrum->toString());
        }
        $this->assertFalse($breadcrum->hasAny());
        $this->assertEmpty($breadcrum->toArray());
        $this->assertEmpty($breadcrum->toString());
        $this->expectException(BreadcrumException::class);
        $breadcrum->withRemovedItem($pos);
    }
}
