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

use Chevere\Interfaces\Message\MessageInterface;
use Colors\Color;

final class Message implements MessageInterface
{
    private array $trTable = [];

    public function __construct(
        private string $template
    ) {
    }

    /**
     * @codeCoverageIgnore
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function template(): string
    {
        return $this->template;
    }

    public function trTable(): array
    {
        return $this->trTable;
    }

    public function toConsole(): string
    {
        $tr = [];
        $color = new Color();
        $color->setUserStyles(self::CLI_TABLE);
        foreach ($this->trTable as $wildcard => $formatting) {
            $format = $formatting[0];
            $colorTheme = 'message_' . $format;
            $tr[$wildcard] = (string) $color($formatting[1])->apply($colorTheme);
        }

        return strtr($this->template, $tr);
    }

    public function toHtml(): string
    {
        $tr = [];
        foreach ($this->trTable as $search => $format) {
            $tag = $format[0];
            $html = self::HTML_TABLE[$tag] ?? null;
            if (isset($html)) {
                $tag = $html;
            }
            $replace = $format[1];
            $tr[$search] = "<${tag}>${replace}</${tag}>";
        }

        return strtr($this->template, $tr);
    }

    public function toString(): string
    {
        $tr = [];
        foreach ($this->trTable as $search => $format) {
            $tr[$search] = $format[1];
        }

        return strtr($this->template, $tr);
    }

    public function strtr(string $search, string $replace): MessageInterface
    {
        return (clone $this)
            ->put('', $search, $replace);
    }

    public function emphasis(string $search, string $replace): MessageInterface
    {
        return (clone $this)
            ->put('emphasis', $search, $replace);
    }

    public function strong(string $search, string $replace): MessageInterface
    {
        return (clone $this)
            ->put('strong', $search, $replace);
    }

    public function underline(string $search, string $replace): MessageInterface
    {
        return (clone $this)
            ->put('underline', $search, $replace);
    }

    public function code(string $search, string $replace): MessageInterface
    {
        return (clone $this)
            ->put('code', $search, $replace);
    }

    private function put(string $format, string $search, string $replace): MessageInterface
    {
        $new = clone $this;
        $new->trTable[$search] = [$format, $replace];

        return $new;
    }
}
