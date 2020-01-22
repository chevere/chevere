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

use Chevere\Components\ExceptionHandler\Exception;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Request;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Runtime\Interfaces\RuntimeInterface;
use Chevere\Components\Runtime\Runtime;
use DateTimeInterface;
use LogicException;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

final class ExceptionHandlerTest extends TestCase
{
    private function getExceptionHandler(): ExceptionHandlerInterface
    {
        return new ExceptionHandler(new LogicException('Ups', 100));
    }

    public function testConstruct(): void
    {
        $handler = $this->getExceptionHandler();
        $this->assertInstanceOf(DateTimeInterface::class, $handler->dateTimeUtc());
        $this->assertInstanceOf(Exception::class, $handler->exception());
        $this->assertIsString($handler->id());
        // $this->assertFalse($handler->hasRuntime());
        $this->assertFalse($handler->hasRequest());
        $this->assertFalse($handler->hasLogger());
        $this->assertFalse($handler->isDebug());
    }

    public function testWithDebug(): void
    {
        $handler = $this->getExceptionHandler()
            ->withIsDebug(true);
        $this->assertTrue($handler->isDebug());
    }

    // public function testWithRuntime(): void
    // {
    //     $handler = $this->getExceptionHandler()
    //         ->withRuntime(new Runtime());
    //     $this->assertTrue($handler->hasRuntime());
    //     $this->assertInstanceOf(RuntimeInterface::class, $handler->runtime());
    // }

    public function testWithRequest(): void
    {
        $handler = $this->getExceptionHandler()
            ->withRequest(new Request(new Method('GET'), new PathUri('/')));
        $this->assertTrue($handler->hasRequest());
        $this->assertInstanceOf(RequestInterface::class, $handler->request());
    }

    public function testWithLoger(): void
    {
        $handler = $this->getExceptionHandler()
            ->withLogger(new Logger(__METHOD__));
        $this->assertTrue($handler->hasLogger());
        $this->assertInstanceOf(Logger::class, $handler->logger());
    }
}
