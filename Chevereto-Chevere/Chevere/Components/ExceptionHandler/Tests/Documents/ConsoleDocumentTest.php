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

use Chevere\Components\ExceptionHandler\Documents\ConsoleDocument;
use Chevere\Components\ExceptionHandler\Exception;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleDocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $document =
            (new ConsoleDocument(
                new ExceptionHandler(
                    new Exception(
                        new LogicException('Ups', 100)
                    )
                )
            ))
            ->withVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE)
            ->toString();

        echo $document . "\n";
    }
}
