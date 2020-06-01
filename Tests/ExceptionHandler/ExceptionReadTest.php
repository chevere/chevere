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

use Chevere\Components\ExceptionHandler\ExceptionRead;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception as CoreException;
use Chevere\Interfaces\ExceptionHandler\ExceptionReadInterface;
use ErrorException;
use Exception;
use PHPUnit\Framework\TestCase;

final class ExceptionReadTest extends TestCase
{
    public function testPhpException(): void
    {
        $code = 100;
        $message = new Message('Ups');
        $exception = new Exception($message->toString(), $code);
        $read = new ExceptionRead($exception);
        $this->assertSame($code, $read->code());
        $this->assertSame(get_class($exception), $read->className());
        $this->assertSame(ExceptionReadInterface::DEFAULT_ERROR_TYPE, $read->severity());
        $this->assertSame(ExceptionReadInterface::ERROR_LEVELS[$read->severity()], $read->loggerLevel());
        $this->assertSame(ExceptionReadInterface::ERROR_TYPES[$read->severity()], $read->type());
        $this->assertSame($exception->getFile(), $read->file());
        $this->assertSame($exception->getLine(), $read->line());
        $this->assertSame($exception->getTrace(), $read->trace());
        $this->assertEquals($message, $read->message());
    }

    public function testErrorException(): void
    {
        $exception = new ErrorException('message', 0, 1);
        $read = new ExceptionRead($exception);
        $this->assertSame($exception->getSeverity(), $read->severity());
        $this->assertSame($exception->getSeverity(), $read->code());
    }

    public function testChevereException(): void
    {
        $message = new Message('Ups');
        $exception = new CoreException($message);
        $read = new ExceptionRead($exception);
        $this->assertSame($message, $read->message());
    }
}
