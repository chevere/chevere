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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use PHPUnit\Framework\TestCase;
use Chevere\Components\Route\RouteName;

final class RouteNameTest extends TestCase
{
    public function testConstructWithInvalidName(): void
    {
        $this->expectException(RouteInvalidNameException::class);
        new RouteName('$');
    }

    public function testconstruct(): void
    {
        $name = 'test';
        $routeName = new RouteName($name);
        $this->assertSame($name, $routeName->toString());
    }
}
