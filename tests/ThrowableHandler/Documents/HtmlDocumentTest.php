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

use function Chevere\Components\Filesystem\fileForPath;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerHtmlDocument;
use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerPlainDocument;
use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerHtmlFormatter;
use Chevere\Components\ThrowableHandler\ThrowableHandler;
use Chevere\Components\ThrowableHandler\ThrowableRead;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;

final class HtmlDocumentTest extends TestCase
{
    private ThrowableHandlerInterface $exceptionHandler;

    protected function setUp(): void
    {
        $this->exceptionHandler = new ThrowableHandler(new ThrowableRead(
            new LogicException(
                'Ups',
                1000,
                new Exception(
                    'Previous',
                    100,
                    new Exception('Pre-previous', 10)
                )
            )
        ));
    }

    public function testHandlerDebugOn(): void
    {
        $this->exceptionHandler = $this->exceptionHandler->withIsDebug(true);
        $document = new ThrowableHandlerHtmlDocument($this->exceptionHandler);
        $this->assertInstanceOf(ThrowableHandlerHtmlFormatter::class, $document->getFormatter());
        $sectionTitle = $document->getSectionTitle();
        $plainDocument = new ThrowableHandlerPlainDocument($this->exceptionHandler);
        $this->assertTrue(strlen($sectionTitle) > $plainDocument->getSectionTitle());
        $string = $document->toString();
        $this->assertStringContainsString('<html><head><meta charset="utf-8">', $string);
        $this->assertStringContainsString('<main class="main--stack">', $string);
        // $file = fileForPath(__DIR__ . '/html.html');
        // $file->put($string);
    }

    public function testHandlerDebugOff(): void
    {
        $this->exceptionHandler = $this->exceptionHandler->withIsDebug(false);
        $document = new ThrowableHandlerHtmlDocument($this->exceptionHandler);
        $this->assertInstanceOf(ThrowableHandlerHtmlFormatter::class, $document->getFormatter());
        $sectionTitle = $document->getSectionTitle();
        $plainDocument = new ThrowableHandlerPlainDocument($this->exceptionHandler);
        $this->assertTrue(strlen($sectionTitle) > $plainDocument->getSectionTitle());
        $string = $document->toString();
        $this->assertStringContainsString('<html><head><meta charset="utf-8">', $string);
        $this->assertStringContainsString('Something went wrong', $string);
        $this->assertStringContainsString('Please try again later.', $string);
        $this->assertStringContainsString('<main class="user-select-none"><div>', $string);
        $this->assertStringContainsString('<p class="fine-print user-select-all">', $string);
    }
}
