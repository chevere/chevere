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

namespace Chevere\Components\Breadcrumb\Tests;

use Chevere\Components\Breadcrumb\Breadcrumb;
use Chevere\Components\Breadcrumb\Exceptions\BreadcrumbException;
use PHPUnit\Framework\TestCase;

final class BreadcrumbTest extends TestCase
{
    private function getItems(): array
    {
        return [
            'test-0',
            'test-1',
            'test-2',
        ];
    }

    public function testConstruct(): void
    {
        $breadcrumb = new Breadcrumb();
        $this->assertEmpty($breadcrumb->toArray());
        $this->assertEmpty($breadcrumb->toString());
        $this->assertFalse($breadcrumb->hasAny());
        $this->assertFalse($breadcrumb->has(0));
        $this->assertSame(-1, $breadcrumb->pos());
    }

    public function testWithAddedItems(): void
    {
        $items = $this->getItems();
        $breadcrumb = new Breadcrumb();
        foreach ($items as $pos => $item) {
            $breadcrumb = $breadcrumb
                ->withAddedItem($item);
            $this->assertTrue($breadcrumb->has($pos));
            $this->assertSame($pos, $breadcrumb->pos());
            $this->assertContains($item, $breadcrumb->toArray());
            $this->assertStringContainsString($item, $breadcrumb->toString());
        }
        $this->assertSame($items, $breadcrumb->toArray());
        $breadcrumb = $breadcrumb
            ->withRemovedItem(1);
        $this->assertNotContains($items[1], $breadcrumb->toArray());
        $this->assertStringNotContainsString($items[1], $breadcrumb->toString());
    }

    public function testWithRemovedItems(): void
    {
        $items = $this->getItems();
        $breadcrumb = new Breadcrumb();
        $pos = 0;
        foreach ($items as $pos => $item) {
            $breadcrumb = $breadcrumb
                ->withAddedItem($item)
                ->withRemovedItem($pos);
            $this->assertFalse($breadcrumb->has($pos));
            $this->assertNotContains($item, $breadcrumb->toArray());
            $this->assertStringNotContainsString($item, $breadcrumb->toString());
        }
        $this->assertFalse($breadcrumb->hasAny());
        $this->assertEmpty($breadcrumb->toArray());
        $this->assertEmpty($breadcrumb->toString());
        $this->expectException(BreadcrumbException::class);
        $breadcrumb->withRemovedItem($pos);
    }
}
