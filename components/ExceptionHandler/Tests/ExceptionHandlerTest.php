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

namespace Chevere\Components\ExceptionHandler\Tests;

use BadMethodCallException;
use Chevere\Components\ExceptionHandler\Exception;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Request;
use Chevere\Components\Route\PathUri;
use DateTimeInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

final class ExceptionHandlerTest extends TestCase
{
    private function getExceptionHandler(): ExceptionHandlerInterface
    {
        return
            new ExceptionHandler(
                new Exception(new LogicException('Ups', 100))
            );
    }

    public function testConstruct(): void
    {
        $handler = $this->getExceptionHandler();
        $this->assertInstanceOf(DateTimeInterface::class, $handler->dateTimeUtc());
        $this->assertInstanceOf(Exception::class, $handler->exception());
        $this->assertIsString($handler->id());
        $this->assertSame('/dev/null', $handler->logDestination());
        $this->assertFalse($handler->isDebug());
        $this->assertFalse($handler->hasRequest());
        $this->expectException(BadMethodCallException::class);
        $handler->request();
    }

    public function testWithDebug(): void
    {
        $handler = $this->getExceptionHandler()
            ->withIsDebug(true);
        $this->assertTrue($handler->isDebug());
    }

    public function testWithRequest(): void
    {
        $handler = $this->getExceptionHandler()
            ->withRequest(new Request(new GetMethod, new PathUri('/')));
        $this->assertTrue($handler->hasRequest());
        $this->assertInstanceOf(RequestInterface::class, $handler->request());
    }

    public function testWithLogDestination(): void
    {
        $destination = 'A logger destination string';
        $handler = $this->getExceptionHandler()
            ->withLogDestination($destination);
        $this->assertSame($destination, $handler->logDestination());
    }
}
