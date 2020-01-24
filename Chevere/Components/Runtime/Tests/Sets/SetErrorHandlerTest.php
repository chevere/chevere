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

namespace Chevere\Components\Runtime\Tests\Sets;

use InvalidArgumentException;
use Chevere\Components\Runtime\Sets\SetErrorHandler;
use PHPUnit\Framework\TestCase;

final class SetErrorHandlerTest extends TestCase
{
    private function getCurrentHandler()
    {
        $current = set_error_handler($this->getNamedDummyHandler());
        restore_error_handler();

        return $current;
    }

    private function getNamedDummyHandler(): string
    {
        return __CLASS__ . '::dummyHandler';
    }

    public static function dummyHandler()
    {
    }

    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetErrorHandler('invalid argument');
    }

    public function testConstruct(): void
    {
        $handler = $this->getNamedDummyHandler();
        $set = new SetErrorHandler($handler);
        $this->assertSame('errorHandler', $set->name());
        $this->assertSame($handler, $set->value());
        $this->assertSame($handler, $this->getCurrentHandler());
    }

    public function testConstructRestoreHandler(): void
    {
        $contextHandler = $this->getCurrentHandler();
        $change = new SetErrorHandler($this->getNamedDummyHandler());
        $changedHandler = $this->getCurrentHandler();
        $this->assertSame($changedHandler, $change->value());
        $restore = new SetErrorHandler('');
        $restoredHandler = $this->getCurrentHandler();
        $this->assertSame($restoredHandler, $restore->handler());
        $this->assertSame(
            is_string($restore->handler()) ? $restoredHandler : '@',
            $restore->value()
        );
        $this->assertSame($contextHandler, $restoredHandler);
    }
}
