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

namespace Chevere\Tests\Controller;

use Chevere\Tests\Controller\_resources\TestHttpRedirectController;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\TestCase;

final class HttpRedirectControllerTest extends TestCase
{
    public function testDefault(): void
    {
        $controller = new TestHttpRedirectController();
        $this->assertSame(302, $controller->status());
        $this->expectException(LogicException::class);
        $controller->uri();
    }

    public function testWithUri(): void
    {
        $controller = new TestHttpRedirectController();
        $uri = new Uri('https://chevere.org');
        $controllerWithUri = $controller->withUri($uri);
        $this->assertNotSame($controller, $controllerWithUri);
        $this->assertNotEquals($controller, $controllerWithUri);
        $this->assertSame($uri, $controllerWithUri->uri());
    }

    public function testWithStatus(): void
    {
        $status = 301;
        $controller = new TestHttpRedirectController();
        $controllerWitStatus = $controller->withStatus($status);
        $this->assertNotSame($controller, $controllerWitStatus);
        $this->assertNotEquals($controller, $controllerWitStatus);
        $this->assertSame($status, $controllerWitStatus->status());
        $this->expectException(InvalidArgumentException::class);
        $controller->withStatus(200);
    }
}
