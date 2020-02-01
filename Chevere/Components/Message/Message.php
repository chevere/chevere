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

use Chevere\Components\App\Instances\BootstrapInstance;
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
 * @method Message MessageContact code(string $search, string $replace) Wraps found $replace in a `code` tag
 * @method Message MessageContact strong(string $search, string $replace) Wraps found $replace in a `strong` tag
 * @method Message MessageContact *any*(string $search, string $replace) Wraps found $replace in a `any` tag
 */
final class Message implements MessageInterface
{
    private string $message;

    private ConsoleColor $consoleColor;

    private string $string;

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

        return $new->strtr($search, $tagged);
    }

    public function strtr(string $search, string $replace): MessageInterface
    {
        $new = clone $this;
        $new->trTable[$search] = $replace;
        $new->message = strtr($new->message, $new->trTable);

        return $new;
    }

    public function toString(): string
    {
        return $this->string ??= $this->cliAware();
    }

    private function cliAware(): string
    {
        $message = $this->message;
        // $hasBoo
        // if (BootstrapInstance::get()->isCli()) {
        //     foreach ($this->consolePallete as $tag => $color) {
        //         $message = preg_replace_callback('#<' . $tag . '>(.*?)<\/' . $tag . '>#', function ($matches) use ($color) {
        //             return $this->consoleColor->apply($color, $matches[1]);
        //         }, $message);
        //     }
        // }

        return $message;
    }
}
