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
use Chevere\Components\Runtime\Sets\SetErrorHandler;
use PHPUnit\Framework\TestCase;

final class SetErrorHandlerTest extends TestCase
{
    private function getCurrentHandler()
    {
        $current = set_error_handler($this->getDummyHandler());
        restore_error_handler();

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
        new SetErrorHandler('invalid argument');
    }

    public function testConstruct(): void
    {
        $handler = $this->getDummyHandler();
        $set = new SetErrorHandler($handler);
        $this->assertSame('errorHandler', $set->name());
        $this->assertSame($handler, $set->value());
        $this->assertSame($handler, $this->getCurrentHandler());
    }

    public function testConstructRestoreHandler(): void
    {
        $contextHandler = $this->getCurrentHandler();
        $change = new SetErrorHandler($this->getDummyHandler());
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
