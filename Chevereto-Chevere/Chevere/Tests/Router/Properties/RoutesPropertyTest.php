<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Router\Properties;

use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Route;
use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Router\Properties\RoutesProperty;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;

final class RoutesPropertyTest extends TestCase
{
    public function testConstructorWithEmpty(): void
    {
        $this->expectException(RouterPropertyException::class);
        new RoutesProperty([]);
    }

    public function testBadConstructorEmpty(): void
    {
        $this->expectException(RouterPropertyException::class);
        new RoutesProperty(['']);
    }

    public function testBadConstructorValue(): void
    {
        $this->expectException(RouterPropertyException::class);
        new RoutesProperty(['test']);
    }

    public function testConstructor(): void
    {
        $route0 = new Route(new PathUri('/test-0'));
        $route1 = new Route(new PathUri('/test-1'));
        $route2 = new Route(new PathUri('/test-2'));
        $array = [
            0 => (new VariableExport($route0))->toSerialize(),
            1 => (new VariableExport($route1))->toSerialize(),
            3 => (new VariableExport($route2))->toSerialize(),
        ];
        $property = new RoutesProperty($array);
        $this->assertSame($array, $property->toArray());
    }
}
