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

namespace Chevere\Tests\Action;

use Chevere\Components\Action\ActionExecuted;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\Exception;
use Error;
use PHPUnit\Framework\TestCase;

final class ActionExecutedTest extends TestCase
{
    public function testConstruct(): void
    {
        $data = ['The data'];
        $executed = new ActionExecuted($data);
        $this->assertSame(0, $executed->code());
        $this->assertSame($data, $executed->data());
        $this->assertFalse($executed->hasThrowable());
        $this->expectException(Error::class);
        $executed->throwable();
    }

    public function testWithThrowable(): void
    {
        $executed = new ActionExecuted([]);
        $throwable = new Exception(new Message('Uy'));
        $executed = $executed->withThrowable($throwable, 1);
        $this->assertTrue($executed->hasThrowable());
        $this->assertSame($throwable, $executed->throwable());
        $this->assertSame(1, $executed->code());
    }
}
