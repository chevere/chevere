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

namespace Chevere\Components\Message;

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Message\Interfaces\MessageInterface;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

/*
 * This class provide a common interface for creating messages.
 *
 * It works by setting a message string and then using chaineable methods it
 * defines a translation string that will be used by toString().
 */

/**
 * @method Message MessageContact code(string $search, string $replace) Wraps found $replace in a `code` tag
 * @method Message MessageContact b(string $search, string $replace) Wraps found $replace in a `b` tag
 * @method Message MessageContact *any*(string $search, string $replace) Wraps found $replace in a `any` tag
 */
final class Message implements MessageInterface
{
    private string $message;

    /** @var array Translation table [search => replace] */
    private array $trTable = [];

    /**
     * Creates a new Message instance.
     *
     * @param string $message The message string
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Magic call method for wrap tags.
     *
     * @param string $tag  Tag name
     * @param array  $args the arguments, being $args[0] (from) and $args[1] (to)
     */
    public function __call(string $tag, array $args): MessageInterface
    {
        $search = (string) $args[0]; // $search String to replace for
        $replace = (string) $args[1]; // $replace String to replace with
        $tagged = '' != $replace ? "<$tag>$replace</$tag>" : '';
        $new = clone $this;
        $new = $new
            ->strtr($search, $tagged);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function strtr(string $search, string $replace): MessageInterface
    {
        $new = clone $this;
        $new->trTable[$search] = $replace;
        $new->message = strtr($new->message, $new->trTable);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function toPlainString(): string
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        if (BootstrapInstance::get()->isCli()) {
            return preg_replace_callback('#<code>(.*?)<\/code>#', function ($matches) {
                $consoleColor = new ConsoleColor();

                return $consoleColor->apply(['light_red'], $matches[1]);
            }, $this->message);
        }

        return $this->message;
    }
}
