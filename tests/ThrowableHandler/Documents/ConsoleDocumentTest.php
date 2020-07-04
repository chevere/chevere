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

namespace Chevere\Tests\ThrowableHandler\Documents;

use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerConsoleDocument;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerPlainDocument;
use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerConsoleFormatter;
use Chevere\Components\ThrowableHandler\ThrowableHandler;
use Chevere\Components\ThrowableHandler\ThrowableRead;
use LogicException;
use PHPUnit\Framework\TestCase;

final class ConsoleDocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $handler = new ThrowableHandler(new ThrowableRead(
            new LogicException('Ups', 100)
        ));
        $document = new ThrowableHandlerConsoleDocument($handler);
        $this->assertInstanceOf(ThrowableHandlerConsoleFormatter::class, $document->getFormatter());
        $sectionTitle = $document->getSectionTitle();
        $plainDocument = new ThrowableHandlerPlainDocument($handler);
        $this->assertTrue(strlen($sectionTitle) > $plainDocument->getSectionTitle());
    }
}
