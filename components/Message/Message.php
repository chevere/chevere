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

/**
 * The Chevere Message
 *
 * Provides generation of system messages with support for HTML phrase tags (em, strong, code, samp, kbd, var).
 *
 * Examples:
 *  - Hello, World!
 *  - File <code>file.php</code> doesn't exists
 *  - No user exists for id <code>123</code>
 *  - User status is <strong>banned</strong>
 *

 */
final class Message implements MessageInterface
{
    private string $template;

    private string $string;

    /** @var array Translation table [search => replace] */
    private array $trTable = [];

    public function __construct(string $template)
    {
        $this->template = $template;
        $this->string = $template;
    }

    public function template(): string
    {
        return $this->template;
    }

    public function trTable(): array
    {
        return $this->trTable;
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function strtr(string $search, string $replace): MessageInterface
    {
        $new = clone $this;
        $new->trTable[$search] = $replace;
        $new->string = strtr($new->string, $new->trTable);

        return $new;
    }

    public function implodeTag(string $search, string $tag, array $array): MessageInterface
    {
        $new = clone $this;
        $oTag = "<$tag>";
        $cTag = "</$tag>";

        return $new->strtr($search, $oTag . implode("$cTag, $oTag", $array) . $cTag);
    }

    public function em(string $search, string $replace): MessageInterface
    {
        $new = clone $this;

        return $new->wrap('em', $search, $replace);
    }

    public function strong(string $search, string $replace): MessageInterface
    {
        $new = clone $this;

        return $new->wrap('strong', $search, $replace);
    }

    public function code(string $search, string $replace): MessageInterface
    {
        $new = clone $this;

        return $new->wrap('code', $search, $replace);
    }

    private function wrap(string $tag, string $search, string $replace): MessageInterface
    {
        $new = clone $this;

        return $new->strtr($search, "<$tag>$replace</$tag>");
    }
}
