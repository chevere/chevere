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

use Chevere\Components\Router\Route\RouteName;
use Chevere\Exceptions\Route\RouteNameInvalidException;
use PHPUnit\Framework\TestCase;

final class RouteNameTest extends TestCase
{
    public function testConstructWithInvalidName(): void
    {
        $this->expectException(RouteNameInvalidException::class);
        new RouteName('$');
    }

    public function testConstruct(): void
    {
        $repo = 'repo';
        $path = '/path/';
        $name = "$repo:$path";
        $routeName = new RouteName($name);
        $this->assertSame($name, $routeName->toString());
        $this->assertSame($repo, $routeName->repository());
        $this->assertSame($path, $routeName->path());
    }
}
