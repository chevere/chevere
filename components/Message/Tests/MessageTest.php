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

use Chevere\Components\Message\Interfaces\MessageInterface;
use Chevere\Components\Message\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testConstruct(): void
    {
        $var = 'message';
        $message = new Message($var);
        $this->assertSame($var, $message->toString());
    }

    public function testTranslate(): void
    {
        $var = 'lorem %translate%';
        $args = ['%translate%', '1'];
        $message = (new Message($var))->strtr(...$args);
        $varTr = strtr($var, [$args[0] => $args[1]]);
        $this->assertSame($varTr, $message->toString());
    }

    public function testWithDeclaredTags(): void
    {
        $var = 'Plain %emphasis% %bold% %underline% %code%';
        $tags = [
            'emphasis' => ['%emphasis%', 'Emphasis,Italic'],
            'strong' => ['%bold%', 'Strong,Bold'],
            'underline' => ['%underline%', 'Underline'],
            'code' => ['%code%', 'Throw new ThisIsTheThig 100']
        ];
        $message = new Message($var);
        $tr = [];
        foreach ($tags as $tag => $value) {
            $message = $message->$tag(...$value);
            $tag = MessageInterface::HTML_TABLE[$tag] ?? $tag;
            $tr[$value[0]] = "<$tag>" . $value[1] . "</$tag>";
        }
        $this->assertSame(strtr($var, $tr), $message->toHtml());
    }

    public function testWithCli(): void
    {
        $search = '%message%';
        $replace = 'word';
        $string = "A $search for CLI awareness";
        $plain = str_replace($search, $replace, $string);
        $message = (new Message($string))->code($search, $replace);
        $this->assertTrue(strlen($plain) == strlen($message->toString()));
    }
}
