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

use Chevere\Components\ExceptionHandler\Formatters\ConsoleFormatter;
use Chevere\Components\ExceptionHandler\Formatters\HtmlFormatter;
use Chevere\Components\ExceptionHandler\Formatters\PlainFormatter;
use Chevere\Components\ExceptionHandler\Trace;
use Exception;
use PHPUnit\Framework\TestCase;

final class TraceTest extends TestCase
{
    public function testConstruct(): void
    {
        $e = new Exception('Message', 100);
        $trace = new Trace($e->getTrace(), new PlainFormatter);
        echo $trace->toString();
        die();
    }
}
