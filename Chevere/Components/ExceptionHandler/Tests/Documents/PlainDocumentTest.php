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

use Chevere\Components\ExceptionHandler\Documents\PlainDocument;
use Chevere\Components\ExceptionHandler\Exception;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\Path\PathApp;
use PHPUnit\Framework\TestCase;
use LogicException;

final class PlainDocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $handler =
            new ExceptionHandler(
                new Exception(
                    new LogicException('Ups', 100)
                )
            );
        // $exception = $handler->exception();
        // $dt = $handler->dateTimeUtc();
        // $absolute = (new PathApp('var/logs/'))->absolute();
        // $logFilename = $absolute . $dt->format('Y/m/d') . '/' . $exception->loggerLevel() . '.log';
        // $handler = $handler->withLogDestination($logFilename);

        $document =
            (new PlainDocument(
                $handler
            ))
            ->toString();

        // echo $document . "\n";
    }
}
