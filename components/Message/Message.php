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

namespace Chevere\Components\Message;

use Chevere\Components\Message\Interfaces\MessageInterface;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

/*
 * This class provide a common interface for creating messages.
 *
 * It works by setting a message string and then using chaineable methods it
 * defines a translation string that will be used by toString().
 *
 *
 */

/**
 * @method Message MessageInterface code(string $search, string $replace) Wraps found $replace in a `code` tag
 * @method Message MessageInterface strong(string $search, string $replace) Wraps found $replace in a `strong` tag
 */
final class Message implements MessageInterface
{
    private string $message;

    private ConsoleColor $consoleColor;

    /** @var array Translation table [search => replace] */
    private array $trTable = [];

    private array $consolePallete = [
        'code' => ['light_red'],
        'strong' => ['bold', 'default'],
    ];

    /**
     * Creates a new Message instance.
     *
     * @param string $message The message string
     */
    public function __construct(string $message)
    {
        $this->message = $message;
        $this->consoleColor = new ConsoleColor;
    }

    public function strtr(string $search, string $replace): MessageInterface
    {
        $new = clone $this;
        $new->trTable[$search] = $replace;
        $new->message = strtr($new->message, $new->trTable);

        return $new;
    }

    public function code(string $search, string $replace): MessageInterface
    {
        $new = clone $this;

        return $new->wrap($search, $replace);
    }

    public function strong(string $search, string $replace): MessageInterface
    {
        $new = clone $this;

        return $new->wrap($search, $replace);
    }

    public function toString(): string
    {
        $message = $this->message;
        // if (BootstrapInstance::get()->isCli()) {
        //     foreach ($this->consolePallete as $tag => $color) {
        //         $message = preg_replace_callback('#<' . $tag . '>(.*?)<\/' . $tag . '>#', function ($matches) use ($color) {
        //             return $this->consoleColor->apply($color, $matches[1]);
        //         }, $message);
        //     }
        // }

        return $message;
    }

    private function wrap(string $search, string $replace): MessageInterface
    {
        $tagged = $replace;
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        if ($bt[1]['function'] && is_string($bt[1]['function'])) {
            $tag = $bt[1]['function'];
            $tagged = "<$tag>$replace</$tag>";
        }
        $new = clone $this;

        return $new->strtr($search, $tagged);
    }
}
