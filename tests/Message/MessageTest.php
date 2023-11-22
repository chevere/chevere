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

namespace Chevere\Tests\Message;

use Chevere\Message\Message;
use PHPUnit\Framework\TestCase;
use function Chevere\Message\message;

final class MessageTest extends TestCase
{
    public function testConstruct(): void
    {
        $var = 'message';
        $message = new Message($var);
        $this->assertSame($var, $message->template());
        $this->assertSame([], $message->trTable());
        $this->assertSame($var, $message->__toString());
        $this->assertSame($var, $message->toConsole());
        $this->assertSame($var, $message->toHtml());
    }

    public function testTranslate(): void
    {
        $search = '%translate%';
        $replace = '1';
        $var = 'string ' . $search;
        $message = new Message($var, translate: $replace);
        $varTr = strtr(
            $var,
            [
                $search => $replace,
            ]
        );
        $this->assertSame($var, $message->template());
        $this->assertSame(
            [
                '%translate%' => $replace,
                '{{translate}}' => $replace,
                '{{ translate }}' => $replace,
            ],
            $message->trTable()
        );
        $this->assertSame($varTr, $message->__toString());
        $this->assertSame($varTr, $message->toConsole());
        $this->assertSame($varTr, $message->toHtml());
    }

    public function testFunction(): void
    {
        $this->assertEquals(message('template'), new Message('template'));
    }
}
