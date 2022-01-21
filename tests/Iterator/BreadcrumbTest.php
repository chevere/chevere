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

namespace Chevere\Tests\Iterator;

use Chevere\Components\Iterator\Breadcrumb;
use Chevere\Exceptions\Core\OutOfRangeException;
use PHPUnit\Framework\TestCase;

final class BreadcrumbTest extends TestCase
{
    public function testConstruct(): void
    {
        $breadcrumb = new Breadcrumb();
        $this->assertEmpty($breadcrumb->toArray());
        $this->assertEmpty($breadcrumb->__toString());
        $this->assertCount(0, $breadcrumb);
        $this->assertFalse($breadcrumb->has(0));
        $this->assertSame(-1, $breadcrumb->pos());
    }

    public function testWithAddedItems(): void
    {
        $items = [
            'test-0',
            'test-1',
            'test-2',
        ];
        $breadcrumb = new Breadcrumb();
        foreach ($items as $pos => $item) {
            $breadcrumb = $breadcrumb
                ->withAddedItem($item);
            $this->assertTrue($breadcrumb->has($pos));
            $this->assertSame($pos, $breadcrumb->pos());
            $this->assertContains($item, $breadcrumb->toArray());
            $this->assertStringContainsString($item, $breadcrumb->__toString());
        }
        $this->assertSame($items, $breadcrumb->toArray());
        $this->assertSame(
            '[' . implode('][', $items) . ']',
            $breadcrumb->__toString()
        );
        $breadcrumb = $breadcrumb
            ->withRemovedItem(1);
        $this->assertNotContains($items[1], $breadcrumb->toArray());
        $this->assertStringNotContainsString($items[1], $breadcrumb->__toString());
    }

    public function testWithRemovedItems(): void
    {
        $items = [
            'test-0',
            'test-1',
            'test-2',
        ];
        $breadcrumb = new Breadcrumb();
        $pos = 0;
        foreach ($items as $pos => $item) {
            $breadcrumb = $breadcrumb
                ->withAddedItem($item)
                ->withRemovedItem($pos);
            $this->assertFalse($breadcrumb->has($pos));
            $this->assertNotContains($item, $breadcrumb->toArray());
            $this->assertStringNotContainsString($item, $breadcrumb->__toString());
        }
        $this->assertCount(0, $breadcrumb);
        $this->assertEmpty($breadcrumb->toArray());
        $this->assertEmpty($breadcrumb->__toString());
        $this->expectException(OutOfRangeException::class);
        $breadcrumb->withRemovedItem($pos);
    }
}
