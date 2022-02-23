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

namespace Chevere\Tests\Container;

use Chevere\Container\Container;
use Chevere\Container\Exceptions\ContainerNotFoundException;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    public function testEmpty(): void
    {
        $container = new Container();
        $this->assertFalse($container->has('foo'));
        $this->expectException(ContainerNotFoundException::class);
        $container->get('foo');
    }

    public function testWithPut(): void
    {
        $container = new Container();
        $container = $container->withPut('foo', 'bar');
        $this->assertTrue($container->has('foo'));
        $this->assertSame('bar', $container->get('foo'));
    }
}
