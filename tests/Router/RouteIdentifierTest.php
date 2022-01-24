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

namespace Chevere\Tests\Router;

use Chevere\Router\RouteIdentifier;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
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
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ \$group /');
        new RouteIdentifier('', 'some-name');
    }

    public function testCtypeSpaceGroup(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ \$group /');
        new RouteIdentifier('   ', 'some-name');
    }

    public function testEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ \$name /');
        new RouteIdentifier('some-group', '');
    }

    public function testCtypeSpaceName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ \$name /');
        new RouteIdentifier('some-group', '  ');
    }
}
