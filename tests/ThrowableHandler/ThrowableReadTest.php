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
use Chevere\Throwable\Exceptions\LogicException;
use Chevere\ThrowableHandler\Interfaces\ThrowableReadInterface;
use Chevere\ThrowableHandler\ThrowableRead;
use ErrorException;
use Exception;
use PHPUnit\Framework\TestCase;

final class ThrowableReadTest extends TestCase
{
    public function testEmptyTrace(): void
    {
        $line = __LINE__ + 1;
        $exception = new class() extends Exception {
        };
        $read = new ThrowableRead($exception);
        $expected = [
            'function' => '{main}',
            'file' => __FILE__,
            'line' => $line,
        ];
        $this->assertSame($expected, $read->trace()[0]);
    }

    public function testPhpException(): void
    {
        $code = 100;
        $message = new Message('Ups');
        $exception = new Exception($message->__toString(), $code);
        $read = new ThrowableRead($exception);
        $this->assertSame((string) $code, $read->code());
        $this->assertSame($exception::class, $read->className());
        $this->assertSame(ThrowableReadInterface::DEFAULT_ERROR_TYPE, $read->severity());
        $this->assertSame(ThrowableReadInterface::ERROR_LEVELS[$read->severity()], $read->loggerLevel());
        $this->assertSame(ThrowableReadInterface::ERROR_TYPES[$read->severity()], $read->type());
        $this->assertSame($exception->getFile(), $read->file());
        $this->assertSame($exception->getLine(), $read->line());
        $readTrace = $read->trace();
        array_shift($readTrace);
        $this->assertSame($exception->getTrace(), $readTrace);
        $this->assertSame($message->__toString(), $read->message()->__toString());
    }

    public function testErrorException(): void
    {
        $exception = new ErrorException('message', 0, 1);
        $read = new ThrowableRead($exception);
        $this->assertSame($exception->getSeverity(), $read->severity());
        $this->assertSame((string) $exception->getSeverity(), $read->code());
    }

    public function testChevereException(): void
    {
        $message = new Message('Ups');
        $exception = new LogicException($message);
        $read = new ThrowableRead($exception);
        $this->assertSame($message, $read->message());
    }
}
