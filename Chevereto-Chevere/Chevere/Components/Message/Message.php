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
use JakubOnderka\PhpConsoleColor\ConsoleColor;

/*
 * This class provide a common interface for creating messages.
 *
 * It works by setting a message string and then using chaineable methods it
 * defines a translation string that will be used by __toString().
 *
 * Useful for creating messages that needs to wrapped in different tags
 * and/or need to be translatable (l10n).
 */

/**
 * @method string code(string $search, string $replace)
 * @method string b(string $search, string $replace)
 */
final class Message
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
     * @param string $tag  Tagname
     * @param array  $args the arguments, being $args[0] (from) and $args[1] (to)
     */
    public function __call(string $tag, array $args): self
    {
        $search = (string) $args[0]; // $search String to replace for
        $replace = (string) $args[1]; // $replace String to replace with
        $tagged = $replace != '' ? "<$tag>$replace</$tag>" : '';
        $this->strtr($search, $tagged);

        return $this;
    }

    /**
     * Populate the translation table (search => replaces).
     *
     * @param string $search  the value being searched for, otherwise known as the needle
     * @param string $replace the replacement value that replaces found search values
     */
    public function strtr(string $search, string $replace): self
    {
        $this->trTable[$search] = $replace;

        return $this;
    }

    /**
     * Returns the message output using the translation table.
     */
    public function toString(): string
    {
        if (BootstrapInstance::get()->cli()) {
            $message = strtr($this->message, $this->trTable);
            return preg_replace_callback('#<code>(.*?)<\/code>#', function ($matches) {
                $consoleColor = new ConsoleColor();

                return $consoleColor->apply(['light_blue'], $matches[1]);
            }, $message);
        }
        return strtr($this->message, $this->trTable);
    }
}
