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
        $id = 1;
        $group = 'some-group';
        $name = 'some-name';
        $routeIdentifier = new RouteIdentifier($id, $group, $name);
        $this->assertSame($id, $routeIdentifier->id());
        $this->assertSame($group, $routeIdentifier->group());
        $this->assertSame($name, $routeIdentifier->name());
        $this->assertSame([
            'id' => $id,
            'group' => $group,
            'name' => $name,
        ], $routeIdentifier->toArray());
    }

    public function testInvalidId(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier(-1, 'some-group', 'some-name');
    }

    public function testEmptyGroup(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier(1, '', 'some-name');
    }

    public function testCtypeSpaceGroup(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier(1, '   ', 'some-name');
    }

    public function testEmptyName(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier(1, 'some-group', '');
    }

    public function testCtypeSpaceName(): void
    {
        $this->expectException(RouteIdentifierException::class);
        new RouteIdentifier(1, 'some-group', '  ');
    }
}
