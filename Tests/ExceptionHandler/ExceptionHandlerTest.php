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

namespace Chevere\Tests\ExceptionHandler;

use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\ExceptionHandler\ExceptionRead;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception;
use Chevere\Interfaces\ExceptionHandler\ExceptionHandlerInterface;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

final class ExceptionHandlerTest extends TestCase
{
    private function getExceptionHandler(): ExceptionHandlerInterface
    {
        return
            new ExceptionHandler(
                new ExceptionRead(
                    new Exception(new Message('Ups'), 100)
                )
            );
    }

    public function testConstruct(): void
    {
        $handler = $this->getExceptionHandler();
        $this->assertInstanceOf(DateTimeInterface::class, $handler->dateTimeUtc());
        $this->assertInstanceOf(ExceptionRead::class, $handler->exception());
        $this->assertIsString($handler->id());
        $this->assertFalse($handler->isDebug());
    }

    public function testWithDebug(): void
    {
        $this->assertTrue(
            $this->getExceptionHandler()->withIsDebug(true)->isDebug()
        );
    }
}
