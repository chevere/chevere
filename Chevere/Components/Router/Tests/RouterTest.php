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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterGroups;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterNamed;
use Chevere\Components\Router\RouterRegex;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testConstructor(): void
    {
        $router = new Router();
        $this->assertFalse($router->hasRegex());
        $this->assertFalse($router->hasIndex());
        $this->assertFalse($router->hasNamed());
        $this->assertFalse($router->hasGroups());
        $this->assertFalse($router->canResolve());
    }

    public function testUnableToResolveException(): void
    {
        $router = new Router();
        $this->expectException(RouterException::class);
        $router->resolve(new Uri('/'));
    }

    public function testRegex(): void
    {
        $regex = new RouterRegex(
            new Regex('#^(?|/home/([A-z0-9\\_\\-\\%]+) (*:0)|/ (*:1)|/hello-world (*:2))$#x')
        );
        $router = (new Router())->withRegex($regex);
        $this->assertTrue($router->hasRegex());
        $this->assertSame($regex, $router->regex());
        $this->assertTrue($router->canResolve());
    }

    public function testIndex(): void
    {
        $index = (new RouterIndex)->withAdded('/test', 0, '', '');
        $router = (new Router)->withIndex($index);
        $this->assertTrue($router->hasIndex());
        $this->assertSame($index, $router->index());
    }

    public function testNamed(): void
    {
        $named = (new RouterNamed)->withAdded('test_name', 1);
        $router = (new Router)->withNamed($named);
        $this->assertTrue($router->hasNamed());
        $this->assertSame($named, $router->named());
    }

    public function testGroups(): void
    {
        $groups = (new RouterGroups)->withAdded('test_group', 2);
        $router = (new Router)->withGroups($groups);
        $this->assertTrue($router->hasGroups());
        $this->assertSame($groups, $router->groups());
    }
}
