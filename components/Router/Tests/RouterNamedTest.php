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

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Router\Properties;

use BadMethodCallException;
use Chevere\Components\Router\RouterNamed;
use PHPUnit\Framework\TestCase;

final class RouterNamedTest extends TestCase
{
    public function testConstruct(): void
    {
        $routerNamed = new RouterNamed();
        $this->assertSame([], $routerNamed->toArray());
        $this->assertFalse($routerNamed->has('some-name'));
    }

    public function testGetEmpty(): void
    {
        $name = 'some-name';
        $routerNamed = new RouterNamed();
        $this->expectException(BadMethodCallException::class);
        $routerNamed->get($name);
    }

    public function testGetForIdEmpty(): void
    {
        $id = 0;
        $routerNamed = new RouterNamed();
        $this->expectException(BadMethodCallException::class);
        $routerNamed->getForId($id);
    }

    public function testWithAdded(): void
    {
        $name = 'some-name';
        $id = 101;
        $routerNamed = (new RouterNamed())
                ->withAdded($name, $id);
        $this->assertTrue($routerNamed->has($name));
        $this->assertSame($id, $routerNamed->get($name));
        $this->assertSame($name, $routerNamed->getForId($id));
    }
}
