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

use Chevere\Components\Router\Exceptions\RouteIdentifierException;
use Chevere\Components\Router\RouteIdentifier;
use PHPUnit\Framework\TestCase;

final class RouteIdentifierTest extends TestCase
{
    public function testConstruct(): void
    {
        $group = 'some-group';
        $name = 'some-name';
        $routeIdentifier = new RouteIdentifier($group, $name);
        $this->assertSame($group, $routeIdentifier->group());
        $this->assertSame($name, $routeIdentifier->name());
        $this->assertSame([
            'group' => $group,
            'name' => $name,
        ], $routeIdentifier->toArray());
    }

    public function testEmptyGroup(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier('', 'some-name');
    }

    public function testCtypeSpaceGroup(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier('   ', 'some-name');
    }

    public function testEmptyName(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier('some-group', '');
    }

    public function testCtypeSpaceName(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier('some-group', '  ');
    }
}
