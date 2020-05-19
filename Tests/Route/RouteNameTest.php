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

use Chevere\Exceptions\Route\RouteNameInvalidException;
use PHPUnit\Framework\TestCase;
use Chevere\Components\Route\RouteName;

final class RouteNameTest extends TestCase
{
    public function testConstructWithInvalidName(): void
    {
        $this->expectException(RouteNameInvalidException::class);
        new RouteName('$');
    }

    public function testConstruct(): void
    {
        $name = 'test';
        $routeName = new RouteName($name);
        $this->assertSame($name, $routeName->toString());
    }
}
