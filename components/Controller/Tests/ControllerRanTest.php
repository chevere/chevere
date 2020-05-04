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

namespace Chevere\Components\Controller\Tests;

use Chevere\Components\Controller\ControllerRan;
use Chevere\Components\ExceptionHandler\Exceptions\Exception;
use Error;
use PHPUnit\Framework\TestCase;

final class ControllerRanTest extends TestCase
{
    public function testConstruct(): void
    {
        $data = ['The data'];
        $ran = new ControllerRan($data);
        $this->assertSame(0, $ran->code());
        $this->assertSame($data, $ran->data());
        $this->assertFalse($ran->hasThrowable());
        $this->expectException(Error::class);
        $ran->throwable();
    }

    public function testWithThrowable(): void
    {
        $ran = new ControllerRan([]);
        $throwable = new Exception;
        $ran = $ran->withThrowable($throwable);
        $this->assertTrue($ran->hasThrowable());
        $this->assertSame($throwable, $ran->throwable());
    }
}
