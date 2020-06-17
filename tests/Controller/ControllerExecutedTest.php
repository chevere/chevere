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

namespace Chevere\Tests\Controller;

use Chevere\Components\Controller\ControllerExecuted;
use Chevere\Exceptions\Core\Exception;
use Error;
use PHPUnit\Framework\TestCase;

final class ControllerExecutedTest extends TestCase
{
    public function testConstruct(): void
    {
        $data = ['The data'];
        $ran = new ControllerExecuted($data);
        $this->assertSame(0, $ran->code());
        $this->assertSame($data, $ran->data());
        $this->assertFalse($ran->hasThrowable());
        $this->expectException(Error::class);
        $ran->throwable();
    }

    public function testWithThrowable(): void
    {
        $ran = new ControllerExecuted([]);
        $throwable = new Exception;
        $ran = $ran->withThrowable($throwable);
        $this->assertTrue($ran->hasThrowable());
        $this->assertSame($throwable, $ran->throwable());
    }
}
