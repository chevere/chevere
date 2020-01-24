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
use Chevere\TestApp\App\Middlewares\TestMiddlewareVoid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MiddlewareNameTest extends TestCase
{
    public function testConstructInvalidName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MiddlewareName('$');
    }

    public function testConstruct(): void
    {
        $name = TestMiddlewareVoid::class;
        $middlewareName = new MiddlewareName($name);
        $this->assertSame($name, $middlewareName->toString());
    }
}
