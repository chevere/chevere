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

use Chevere\Message\Interfaces\MessageInterface;
use Chevere\Message\Message;
use function Chevere\Message\message;
use Colors\Color;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testConstruct(): void
    {
        $var = 'message';
        $message = new Message($var);
        $this->assertSame($var, $message->template());
        $this->assertSame([], $message->trTable());
        $this->assertSame($var, $message->__toString());
    }

    public function testTranslate(): void
    {
        $search = '%translate%';
        $replace = '1';
        $var = 'string ' . $search;
        $message = (new Message($var))->withStrtr($search, $replace);
        $varTr = strtr($var, [
            $search => $replace,
        ]);
        $this->assertSame($var, $message->template());
        $this->assertSame([
            '%translate%' => ['', $replace],
        ], $message->trTable());
        $this->assertSame($varTr, $message->__toString());
    }

    public function testWithDeclaredTags(): void
    {
        $var = 'Plain %emphasis% %bold% %underline% %code%';
        $tags = [
            'emphasis' => ['%emphasis%', 'Emphasis,Italic'],
            'strong' => ['%bold%', 'Strong,Bold'],
            'underline' => ['%underline%', 'Underline'],
            'code' => ['%code%', 'Throw new ThisIsTheThing 100'],
        ];
        $message = new Message($var);
        $tr = [];
        foreach ($tags as $tag => $value) {
            $withReplaces = ($withReplaces ?? $message)->{'with' . ucfirst($tag)}(...$value);
            $this->assertNotSame($message, $withReplaces);
            $tag = MessageInterface::HTML_TABLE[$tag] ?? $tag;
            $tr[$value[0]] = "<${tag}>" . $value[1] . "</${tag}>";
        }
        $html = strtr($var, $tr);
        $plain = strip_tags($html);
        $this->assertSame($var, $withReplaces->template());
        $this->assertSame($html, $withReplaces->toHtml());
        $this->assertSame($plain, strval($withReplaces));
        $consoleMessage = $withReplaces->toConsole();
        $consolePlain = preg_replace('/' . preg_quote(Color::ESC) . '\d+m/', '', $consoleMessage);
        $this->assertSame($plain, $consolePlain);
        if ((new Color())->isSupported()) {
            $this->assertNotSame($plain, $consoleMessage);
        }
    }

    public function testFunction(): void
    {
        $this->assertEquals(message('template'), new Message('template'));
    }
}
