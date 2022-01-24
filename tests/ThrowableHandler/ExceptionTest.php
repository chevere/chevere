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

use Chevere\Message\Message;
use Chevere\Throwable\Exceptions\ErrorException;
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\Throwable\Exceptions\RangeException;
use Chevere\ThrowableHandler\Interfaces\ThrowableReadInterface;
use Chevere\ThrowableHandler\ThrowableRead;
use PHPUnit\Framework\TestCase;

final class ExceptionTest extends TestCase
{
    public function testConstructWithException(): void
    {
        $message = new Message('test');
        $code = 12345;
        $exceptionName = LogicException::class;
        // LINE
        $throw = new $exceptionName($message, $code);
        $line = __LINE__ - 1;
        $exception = new ThrowableRead($throw);
        $this->assertSame($exceptionName, $exception->className());
        $this->assertSame($message, $exception->message());
        $this->assertSame((string) $code, $exception->code());
        $this->assertSame(__FILE__, $exception->file());
        $this->assertSame($line, $exception->line());
        $this->assertSame(1, $exception->severity());
        $this->assertSame('critical', $exception->loggerLevel());
        $this->assertSame('Fatal error', $exception->type());
        $this->assertIsArray($exception->trace());
    }

    public function testConstructWithErrorDefaultCode(): void
    {
        $code = ThrowableReadInterface::DEFAULT_ERROR_TYPE;
        $exceptionName = TestErrorException::class;
        $exception = new $exceptionName(new Message('test'));
        $normalized = new ThrowableRead($exception);
        $this->assertSame((string) $code, $normalized->code());
    }

    public function testConstructWithErrorInvalidSeverity(): void
    {
        $exceptionName = TestErrorException::class;
        $exception = new $exceptionName(new Message('test'));
        $exception->setSeverity(12346664321);
        $this->expectException(RangeException::class);
        new ThrowableRead($exception);
    }
}

/**
 * A dummy ErrorException that allows to inject severity values.
 */
final class TestErrorException extends ErrorException
{
    public function setSeverity(int $severity): void
    {
        $this->severity = $severity;
    }
}
