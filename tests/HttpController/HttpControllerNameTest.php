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

namespace Chevere\Tests\HttpController;

use Chevere\Controller\Exceptions\ControllerNameInterfaceException;
use Chevere\Controller\Exceptions\ControllerNameNotExistsException;
use Chevere\HttpController\HttpControllerName;
use Chevere\String\Exceptions\EmptyException;
use Chevere\Tests\Controller\_resources\ControllerTestController;
use Chevere\Tests\HttpController\_resources\TestHttpController;
use PHPUnit\Framework\TestCase;

final class HttpControllerNameTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->expectException(EmptyException::class);
        new HttpControllerName('');
    }

    public function testNotFound(): void
    {
        $this->expectException(ControllerNameNotExistsException::class);
        new HttpControllerName('notFound');
    }

    public function testInterface(): void
    {
        $this->expectException(ControllerNameInterfaceException::class);
        new HttpControllerName(__CLASS__);
    }

    public function testControllerNotHttp(): void
    {
        $this->expectException(ControllerNameInterfaceException::class);
        new HttpControllerName(ControllerTestController::class);
    }

    public function testConstruct(): void
    {
        $controller = new HttpControllerName(TestHttpController::class);
        $this->assertSame(TestHttpController::class, $controller->__toString());
    }
}
