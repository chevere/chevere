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
use Chevere\Components\ThrowableHandler\Formatters\PlainFormatter;
use Chevere\Components\ThrowableHandler\ThrowableHandler;
use Chevere\Components\ThrowableHandler\ThrowableRead;
use Chevere\Interfaces\ThrowableHandler\DocumentInterface;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerInterface;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

final class PlainDocumentTest extends TestCase
{
    private ThrowableHandlerInterface $exceptionHandler;

    public function setUp(): void
    {
        $this->exceptionHandler = new ThrowableHandler(new ThrowableRead(
            new LogicException('Ups', 100)
        ));
    }

    public function testConstruct(): void
    {
        $document = new PlainDocument($this->exceptionHandler);
        $verbosity = 0;
        $this->assertInstanceOf(PlainFormatter::class, $document->getFormatter());
        $this->assertSame($verbosity, $document->verbosity());
        $verbosity = OutputInterface::VERBOSITY_QUIET;
        $document = $document->withVerbosity($verbosity);
        $this->assertSame($verbosity, $document->verbosity());
        $getTemplate = $document->getTemplate();
        $this->assertIsArray($getTemplate);
        $this->assertSame(DocumentInterface::SECTIONS, array_keys($getTemplate));
        $document->toString();
    }

    // public function testHandlerWithRequest(): void
    // {
    //     $pathUri = new PathUri('/' . uniqid('', true));
    //     $request = new Request(new GetMethod, $pathUri);
    //     $this->exceptionHandler = $this->exceptionHandler
    //         ->withRequest($request);
    //     $document = new PlainDocument($this->exceptionHandler);
    //     $this->assertStringContainsString($pathUri->toString(), $document->toString());
    // }
}
