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

use Chevere\Iterator\Breadcrumb;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
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
        $withAdded = $breadcrumb;
        foreach ($items as $pos => $item) {
            $withAdded = $withAdded->withAdded($item);
            $this->assertTrue($withAdded->has($pos));
            $this->assertSame($pos, $withAdded->pos());
            $this->assertContains($item, $withAdded->toArray());
            $this->assertStringContainsString($item, $withAdded->__toString());
        }
        $this->assertNotSame($breadcrumb, $withAdded);
        $this->assertSame($items, $withAdded->toArray());
        $this->assertSame(
            '[' . implode('][', $items) . ']',
            $withAdded->__toString()
        );
        $withRemoved = $withAdded->withRemoved(1);
        $this->assertNotSame($withAdded, $withRemoved);
        $this->assertNotContains($items[1], $withRemoved->toArray());
        $this->assertStringNotContainsString($items[1], $withRemoved->__toString());
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
        foreach ($items as $item) {
            $breadcrumb = $breadcrumb
                ->withAdded($item)
                ->withRemoved($pos);
            $this->assertCount(0, $breadcrumb);
            $this->assertFalse($breadcrumb->has($pos));
            $this->assertNotContains($item, $breadcrumb->toArray());
            $this->assertStringNotContainsString($item, $breadcrumb->__toString());
        }
        $this->assertCount(0, $breadcrumb);
        $this->assertEmpty($breadcrumb->toArray());
        $this->assertEmpty($breadcrumb->__toString());
        $this->expectException(OutOfBoundsException::class);
        $breadcrumb->withRemoved($pos);
    }
}
