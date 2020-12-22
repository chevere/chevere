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

namespace Chevere\Tests\ThrowableHandler;

use Chevere\Components\Message\Message;
use Chevere\Components\ThrowableHandler\ThrowableHandler;
use Chevere\Components\ThrowableHandler\ThrowableRead;
use Chevere\Exceptions\Core\Exception;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

final class ThrowableHandlerTest extends TestCase
{
    public function testConstruct(): void
    {
        $handler = $this->getExceptionHandler();
        $this->assertInstanceOf(DateTimeInterface::class, $handler->dateTimeUtc());
        $this->assertInstanceOf(ThrowableRead::class, $handler->throwableRead());
        $this->assertIsString($handler->id());
        $this->assertTrue($handler->isDebug());
    }

    public function testWithDebug(): void
    {
        $this->assertTrue(
            $this->getExceptionHandler()->withIsDebug(true)->isDebug()
        );
    }

    private function getExceptionHandler(): ThrowableHandlerInterface
    {
        return
            new ThrowableHandler(
                new ThrowableRead(
                    new Exception(new Message('Ups'), 100)
                )
            );
    }
}
