<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Runtime\Sets;

use Chevere\Components\Runtime\Exceptions\InvalidArgumentException;
use Chevere\Components\Runtime\Sets\SetExceptionHandler;
use PHPUnit\Framework\TestCase;

final class SetExceptionHandlerTest extends TestCase
{
    private function getCurrentHandler()
    {
        $current = set_exception_handler($this->getDummyHandler());
        restore_exception_handler();

        return $current;
    }

    private function getDummyHandler(): string
    {
        return __CLASS__ . '::dummyHandler';
    }

    public static function dummyHandler()
    {
    }

    public function testConstructInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SetExceptionHandler('100');
    }

    public function testConstruct(): void
    {
        $handler = $this->getDummyHandler();
        $set = new SetExceptionHandler($handler);
        $this->assertSame('exceptionHandler', $set->name());
        $this->assertSame($handler, $set->value());
        $this->assertSame($handler, $this->getCurrentHandler());
    }

    public function testConstructRestoreHandler(): void
    {
        $contextHandler = $this->getCurrentHandler();
        $change = new SetExceptionHandler($this->getDummyHandler());
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
