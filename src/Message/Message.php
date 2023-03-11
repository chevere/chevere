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

    /**
     * @param string $template A message template, i.e: `Disk %foo% is %percent% full`
     */
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

        return $this->toExport($tr);
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
         * @var string $search
         * @var string[] $replacement
         */
        foreach ($this->trTable as $search => $replacement) {
            $tr[$search] = $replacement[1];
            $format = $replacement[0]; // @codeCoverageIgnore
            $style = "message_{$format}";
            if (array_key_exists($style, self::CLI_TABLE)) {
                $style = self::CLI_TABLE[$style];
                $tr[$search] = (string) $color($replacement[1])->apply($style);
            }
        }

        return $this->toExport($tr);
    }

    public function toHtml(): string
    {
        $tr = [];
        foreach ($this->trTable as $search => $replacement) {
            $tag = $replacement[0];
            $html = self::HTML_TABLE[$tag] ?? null;
            $tag = $html ?? $tag;
            $replace = $replacement[1];
            $tr[$search] = match ($tag) {
                '' => $replace,
                default => "<{$tag}>{$replace}</{$tag}>",
            };
        }

        return $this->toExport($tr);
    }

    public function withTranslate(string $search, string $replace): MessageInterface
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

    /**
     * @param array<string, string> $tr
     */
    private function toExport(array $tr): string
    {
        return strtr($this->template, $tr);
    }
}
