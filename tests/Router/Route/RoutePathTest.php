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

namespace Chevere\Tests\Router\Route;

use Chevere\Router\Route\RoutePath;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RoutePathTest extends TestCase
{
    public function testInvalidPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RoutePath('[{path}]-invalid');
    }

    public function testInvalidOptionalPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RoutePath('invalid-[path]');
    }

    public function testConstruct(): void
    {
        $string = '/path/{id:\d+}/it/{var}';
        $routePath = new RoutePath($string);
        $this->assertTrue($routePath->wildcards()->has('id'));
        $this->assertTrue($routePath->wildcards()->has('var'));
        $this->assertSame('/path/{id}/it/{var}', $routePath->name());
        $this->assertSame(
            '~^(?|/path/(\d+)/it/([^/]+))$~',
            $routePath->regex()->__toString()
        );
        $this->assertSame($string, $routePath->__toString());
    }
}
