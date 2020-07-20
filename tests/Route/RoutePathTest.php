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

namespace Chevere\Tests\Route;

use Chevere\Components\Route\RoutePath;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
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
        $string = 'my-path/{here}';
        $routePath = new RoutePath($string);
        $this->assertTrue($routePath->wildcards()->has('here'));
        $this->assertSame('~^(?|my\-path/([^/]+))$~', $routePath->regex()->toString());
        $this->assertSame($string, $routePath->toString());
    }
}
