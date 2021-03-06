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

use Chevere\Components\ThrowableHandler\Documents\ThrowableHandlerPlainDocument;
use Chevere\Components\ThrowableHandler\Formatters\ThrowableHandlerPlainFormatter;
use Chevere\Components\ThrowableHandler\ThrowableHandler;
use Chevere\Components\ThrowableHandler\ThrowableRead;
use Chevere\Interfaces\ThrowableHandler\ThrowableHandlerDocumentInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PlainDocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $document = new ThrowableHandlerPlainDocument(
            new ThrowableHandler(new ThrowableRead(
                new LogicException(
                    'Ups',
                    1000,
                )
            ))
        );
        $verbosity = 0;
        $this->assertInstanceOf(ThrowableHandlerPlainFormatter::class, $document->getFormatter());
        $this->assertSame($verbosity, $document->verbosity());
        $verbosity = 16;
        $document = $document->withVerbosity($verbosity);
        $this->assertSame($verbosity, $document->verbosity());
        $getTemplate = $document->getTemplate();
        $this->assertIsArray($getTemplate);
        $this->assertSame(ThrowableHandlerDocumentInterface::SECTIONS, array_keys($getTemplate));
        $document->toString();
    }
}
