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

namespace Chevere\Components\Middleware\Tests;

use Chevere\Components\Middleware\MiddlewareName;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use PHPUnit\Framework\TestCase;

final class MiddlewareNameCollectionTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $collection = new MiddlewareNameCollection();
        $this->assertFalse($collection->hasAny());
    }

    public function testConstruct(): void
    {
        $name = new MiddlewareName(TestMiddlewareVoid::class);
        $collection = new MiddlewareNameCollection($name);
        $this->assertTrue($collection->hasAny());
        $this->assertTrue($collection->has($name));
        $this->assertContains($name, $collection->toArray());
    }

    public function testWithAddedMiddlewareName(): void
    {
        $name = new MiddlewareName(TestMiddlewareVoid::class);
        $collection = new MiddlewareNameCollection();
        $collection = $collection
          ->withAddedMiddlewareName($name);
        $this->assertTrue($collection->hasAny());
        $this->assertTrue($collection->has($name));
        $this->assertContains($name, $collection->toArray());
    }
}
