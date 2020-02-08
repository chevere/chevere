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

use Chevere\Components\ExceptionHandler\Documents\HtmlDocument;
use Chevere\Components\ExceptionHandler\Documents\PlainDocument;
use Chevere\Components\ExceptionHandler\Exception;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\ExceptionHandler\Formatters\HtmlFormatter;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use PHPUnit\Framework\TestCase;
use LogicException;

final class HtmlDocumentTest extends TestCase
{
    private ExceptionHandlerInterface $exceptionHandler;

    public function setUp(): void
    {
        $this->exceptionHandler = new ExceptionHandler(new Exception(
            new LogicException('Ups', 100)
        ));
    }

    public function testHandlerDebugOn(): void
    {
        $this->exceptionHandler = $this->exceptionHandler->withIsDebug(true);
        $document = new HtmlDocument($this->exceptionHandler);
        $this->assertInstanceOf(HtmlFormatter::class, $document->getFormatter());
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
        $document = new HtmlDocument($this->exceptionHandler);
        $this->assertInstanceOf(HtmlFormatter::class, $document->getFormatter());
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
