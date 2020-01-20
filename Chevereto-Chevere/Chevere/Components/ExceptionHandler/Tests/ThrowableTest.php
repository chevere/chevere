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

use LogicException;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionInterface;
use Chevere\Components\ExceptionHandler\Tests\resources\TestErrorException;
use Chevere\Components\ExceptionHandler\Throwable;
use PHPUnit\Framework\TestCase;

final class ThrowableTest extends TestCase
{
    public function testConstructWithException(): void
    {
        $message = 'test';
        $code = 12345;
        $exceptionName = LogicException::class;
        $exception = new $exceptionName($message, $code); // LINE
        $line = __LINE__ - 1;
        $throwable = new Throwable($exception);
        $this->assertSame($exceptionName, $throwable->className());
        $this->assertSame($message, $throwable->message());
        $this->assertSame($code, $throwable->code());
        $this->assertSame(__FILE__, $throwable->file());
        $this->assertSame($line, $throwable->line());
    }

    public function testConstructWithErrorDefaultCode(): void
    {
        $code = ExceptionInterface::DEFAULT_ERROR_TYPE;
        $exceptionName = TestErrorException::class;
        $exception = new $exceptionName('test');
        $throwable = new Throwable($exception);
        $this->assertSame($code, $throwable->code());
    }

    public function testConstructWithErrorInvalidSeverity(): void
    {
        $exceptionName = TestErrorException::class;
        $exception = new $exceptionName('test');
        $exception->setSeverity(12346664321);
        $this->expectException(LogicException::class);
        new Throwable($exception);
    }
}
