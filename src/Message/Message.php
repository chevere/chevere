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

namespace Chevere\Message;

use Chevere\Message\Interfaces\MessageInterface;
use Colors\Color;

final class Message implements MessageInterface
{
    /**
     * @var array<string, string[]>
     */
    private array $trTable = [];

    public function __construct(
        private string $template
    ) {
    }

    public function __toString(): string
    {
        $tr = [];
        foreach ($this->trTable as $search => $format) {
            $tr[$search] = $format[1];
        }

        return strtr($this->template, $tr);
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
        $color = (new Color(''))->setUserStyles(self::CLI_TABLE);
        /**
         * @var string $wildcard
         * @var string[] $formatting
         */
        foreach ($this->trTable as $wildcard => $formatting) {
            $format = $formatting[0] ?? '';
            $colorTheme = 'message_' . $format;
            if (array_key_exists($colorTheme, self::CLI_TABLE)) {
                $colorTheme = self::CLI_TABLE[$colorTheme];
                $tr[$wildcard] = (string) $color($formatting[1])->apply($colorTheme);
            }
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

    public function withStrtr(string $search, string $replace): MessageInterface
    {
        return $this->put('', $search, $replace);
    }

    public function withEmphasis(string $search, string $replace): MessageInterface
    {
        return $this->put('emphasis', $search, $replace);
    }

    public function withStrong(string $search, string $replace): MessageInterface
    {
        return $this->put('strong', $search, $replace);
    }

    public function withUnderline(string $search, string $replace): MessageInterface
    {
        return $this->put('underline', $search, $replace);
    }

    public function withCode(string $search, string $replace): MessageInterface
    {
        return $this->put('code', $search, $replace);
    }

    private function put(string $format, string $search, string $replace): MessageInterface
    {
        $new = clone $this;
        $new->trTable[$search] = [$format, $replace];

        return $new;
    }
}
