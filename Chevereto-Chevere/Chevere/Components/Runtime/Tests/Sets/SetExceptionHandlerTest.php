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
use Chevere\Components\Runtime\Sets\SetExceptionHandler;
use PHPUnit\Framework\TestCase;

final class SetExceptionHandlerTest extends TestCase
{
    private function getCurrentHandler()
    {
        $current = set_exception_handler($this->getNamedDummyHandler());
        restore_exception_handler();

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
        new SetExceptionHandler('invalid argument');
    }

    public function testConstruct(): void
    {
        $handler = $this->getNamedDummyHandler();
        $set = new SetExceptionHandler($handler);
        $this->assertSame('exceptionHandler', $set->name());
        $this->assertSame($handler, $set->value());
        $this->assertSame($handler, $this->getCurrentHandler());
    }

    public function testConstructRestoreHandler(): void
    {
        $contextHandler = $this->getCurrentHandler();
        $change = new SetExceptionHandler($this->getNamedDummyHandler());
        $changedHandler = $this->getCurrentHandler();
        $this->assertSame($changedHandler, $change->value());
        $restore = new SetExceptionHandler('');
        $restoredHandler = $this->getCurrentHandler();
        $this->assertSame($restoredHandler, $restore->handler());
        $this->assertSame(
            is_string($restore->handler()) ? $restoredHandler : '@',
            $restore->value()
        );
        $this->assertSame($contextHandler, $restoredHandler);
    }
}
