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
use Colors\Color;
use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;

final class ConsoleDocumentTest extends TestCase
{
    public function testConstruct(): void
    {
        $handler = new ThrowableHandler(new ThrowableRead(
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
        $document = new ThrowableHandlerConsoleDocument($handler);
        $this->assertSame(
            (new Color())->isSupported()
                ? '[31m[1m%title%[0m[0m in [34m[4m%fileLine%[0m[0m'
                : '%title% in %fileLine%',
            $document->getSectionTitle()
        );
        $this->assertInstanceOf(
            ThrowableHandlerConsoleFormatter::class,
            $document->getFormatter()
        );
        $sectionTitle = $document->getSectionTitle();
        $plainDocument = new ThrowableHandlerPlainDocument($handler);
        $this->assertTrue(
            strlen($sectionTitle) > $plainDocument->getSectionTitle()
        );
        $document->__toString();
    }
}
