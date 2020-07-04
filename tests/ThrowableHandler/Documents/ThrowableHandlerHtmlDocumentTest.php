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

use Chevere\Components\ThrowableHandler\Documents\PlainDocument;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerHtmlDocument;
use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerHtmlFormatter;
use Chevere\Components\ThrowableHandler\ThrowableHandler;
use Chevere\Components\ThrowableHandler\ThrowableRead;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

final class ThrowableHandlerHtmlDocumentTest extends TestCase
{
    private ThrowableHandlerInterface $exceptionHandler;

    public function setUp(): void
    {
        $this->exceptionHandler = new ThrowableHandler(new ThrowableRead(
            new LogicException('Ups', 100)
        ));
    }

    public function testHandlerDebugOn(): void
    {
        $this->exceptionHandler = $this->exceptionHandler->withIsDebug(true);
        $document = new ThrowableHandlerHtmlDocument($this->exceptionHandler);
        $this->assertInstanceOf(ThrowableHandlerHtmlFormatter::class, $document->getFormatter());
        $sectionTitle = $document->getSectionTitle();
        $plainDocument = new PlainDocument($this->exceptionHandler);
        $this->assertTrue(strlen($sectionTitle) > $plainDocument->getSectionTitle());
        $string = $document->toString();
        $this->assertStringContainsString('<html><head><meta charset="utf-8">', $string);
        $this->assertStringContainsString('<main class="main--stack">', $string);
    }

    public function testHandlerDebugOff(): void
    {
        $this->exceptionHandler = $this->exceptionHandler->withIsDebug(false);
        $document = new ThrowableHandlerHtmlDocument($this->exceptionHandler);
        $this->assertInstanceOf(ThrowableHandlerHtmlFormatter::class, $document->getFormatter());
        $sectionTitle = $document->getSectionTitle();
        $plainDocument = new PlainDocument($this->exceptionHandler);
        $this->assertTrue(strlen($sectionTitle) > $plainDocument->getSectionTitle());
        $string = $document->toString();
        $this->assertStringContainsString('<html><head><meta charset="utf-8">', $string);
        $this->assertStringContainsString('Something went wrong', $string);
        $this->assertStringContainsString('The system has failed', $string);
        $this->assertStringContainsString('<main><div>', $string);
    }
}
