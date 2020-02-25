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
use Chevere\Components\ExceptionHandler\Formatters\PlainFormatter;
use Chevere\Components\ExceptionHandler\Interfaces\DocumentInterface;
use Chevere\Components\ExceptionHandler\Interfaces\ExceptionHandlerInterface;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Request;
use Chevere\Components\Route\PathUri;
use PHPUnit\Framework\TestCase;
use LogicException;
use Symfony\Component\Console\Output\OutputInterface;

final class PlainDocumentTest extends TestCase
{
    private ExceptionHandlerInterface $exceptionHandler;

    public function setUp(): void
    {
        $this->exceptionHandler = new ExceptionHandler(new Exception(
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

    public function testHandlerWithRequest(): void
    {
        $pathUri = new PathUri('/' . uniqid('', true));
        $request = new Request(new GetMethod, $pathUri);
        $this->exceptionHandler = $this->exceptionHandler
            ->withRequest($request);
        $document = new PlainDocument($this->exceptionHandler);
        $this->assertStringContainsString($pathUri->toString(), $document->toString());
    }
}
