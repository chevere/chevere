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

namespace Chevere\Components\Message\Tests;

use Chevere\Components\Message\Exceptions\MessageSearchNotExistsException;
use Chevere\Components\Message\Interfaces\MessageInterface;
use Chevere\Components\Message\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testConstruct(): void
    {
        $var = 'message';
        $message = new Message($var);
        $this->assertSame($var, $message->template());
        $this->assertSame([], $message->trTable());
        $this->assertSame($var, $message->toString());
    }

    public function testTranslate(): void
    {
        $search = '%translate%';
        $replace = '1';
        $var = 'string ' . $search;
        $message = (new Message($var))->strtr($search, $replace);
        $varTr = strtr($var, [$search => $replace]);
        $this->assertSame($var, $message->template());
        $this->assertSame([
            '%translate%' => ['', $replace]
        ], $message->trTable());
        $this->assertSame($varTr, $message->toString());
    }

    public function testWithDeclaredTags(): void
    {
        $var = 'Plain %emphasis% %bold% %underline% %code%';
        $tags = [
            'emphasis' => ['%emphasis%', 'Emphasis,Italic'],
            'strong' => ['%bold%', 'Strong,Bold'],
            'underline' => ['%underline%', 'Underline'],
            'code' => ['%code%', 'Throw new ThisIsTheThing 100']
        ];
        $message = new Message($var);
        $tr = [];
        foreach ($tags as $tag => $value) {
            $message = $message->$tag(...$value);
            $tag = MessageInterface::HTML_TABLE[$tag] ?? $tag;
            $tr[$value[0]] = "<$tag>" . $value[1] . "</$tag>";
        }
        $this->assertSame($var, $message->template());
        $html = strtr($var, $tr);
        $this->assertSame($html, $message->toHtml());
        $plain = strip_tags($html);
        $this->assertSame($plain, $message->toString());
        $this->assertNotSame($plain, $message->toConsole());
    }

    // public function testWithWrongSearch(): void
    // {
    //     $message = new Message('Hello, World!');
    //     $this->expectException(MessageSearchNotExistsException::class);
    //     $message->code('%search%', 'replace');
    // }
}
