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

namespace Chevere\Tests\Http;

use Chevere\Http\MiddlewareName;
use Chevere\Tests\Http\_resources\MiddlewareTest;
use PHPUnit\Framework\TestCase;
use Throwable;

final class MiddlewareNameTest extends TestCase
{
    public function testInvalid(): void
    {
        $this->expectException(Throwable::class);
        new MiddlewareName('');
    }

    public function testConstruct(): void
    {
        $middleware = MiddlewareTest::class;
        $name = new MiddlewareName($middleware);
        $this->assertSame($middleware, $name->__toString());
    }
}
