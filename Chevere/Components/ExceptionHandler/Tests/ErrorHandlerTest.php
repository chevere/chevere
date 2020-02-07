<?php
namespace Chevere\Components\ExceptionHandler\Tests;

use Chevere\Components\ExceptionHandler\ErrorHandler;
use ErrorException;
use PHPUnit\Framework\TestCase;

final class ErrorHandlerTest extends TestCase
{
    public function testStaticError(): void
    {
        $this->expectException(ErrorException::class);
        ErrorHandler::error(1, 'Error', __FILE__, __LINE__);
    }
}
