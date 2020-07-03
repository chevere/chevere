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
        $executed = new ControllerExecuted($data);
        $this->assertSame(0, $executed->code());
        $this->assertSame($data, $executed->data());
        $this->assertFalse($executed->hasThrowable());
        $this->expectException(Error::class);
        $executed->throwable();
    }

    public function testWithThrowable(): void
    {
        $executed = new ControllerExecuted([]);
        $throwable = new Exception;
        $executed = $executed->withThrowable($throwable, 1);
        $this->assertTrue($executed->hasThrowable());
        $this->assertSame($throwable, $executed->throwable());
        $this->assertSame(1, $executed->code());
    }
}
