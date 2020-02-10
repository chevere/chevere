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
use Chevere\Components\ExceptionHandler\Documents\PlainDocument;
use Chevere\Components\ExceptionHandler\Exception;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\ExceptionHandler\Formatters\ConsoleFormatter;
use LogicException;
use PHPUnit\Framework\TestCase;

final class ConsoleDocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $handler = new ExceptionHandler(new Exception(
            new LogicException('Ups', 100)
        ));
        $document = new ConsoleDocument($handler);
        $this->assertInstanceOf(ConsoleFormatter::class, $document->getFormatter());
        $sectionTitle = $document->getSectionTitle();
        $plainDocument = new PlainDocument($handler);
        $this->assertTrue(strlen($sectionTitle) > $plainDocument->getSectionTitle());
    }
}
