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

namespace Chevere\Components\Message\Tests;

use Chevere\Components\Message\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testConstruct(): void
    {
        $var = 'message';
        $message = new Message($var);
        $this->assertSame($var, $message->toPlainString());
        $this->assertSame($var, $message->toString());
    }

    public function testTranslate(): void
    {
        $var = 'lorem %translate%';
        $args = ['%translate%', '1'];
        $message = (new Message($var))
            ->strtr(...$args);
        $varTr = strtr($var, [$args[0] => $args[1]]);
        $this->assertSame($varTr, $message->toPlainString());
        $this->assertSame($varTr, $message->toString());
    }

    public function testWithDeclaredTags(): void
    {
        $var = 'lorem %strong% %message%';
        $tags = [
            'b' => ['%strong%', 'Bold,Emphasis'],
            'code' => ['%message%', '100']
        ];
        $message = new Message($var);
        $tr = [];
        foreach ($tags as $tag => $value) {
            $message = $message->$tag(...$value);
            $tr[$value[0]] = "<$tag>" . $value[1] . "</$tag>";
            $this->assertSame(
                strtr($var, $tr),
                $message->toPlainString()
            );
        }
    }
}
