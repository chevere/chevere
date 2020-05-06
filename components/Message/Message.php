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

use Ahc\Cli\Output\Color;
use Chevere\Components\Message\Interfaces\MessageInterface;

final class Message implements MessageInterface
{
    private string $template;

    /** @var array Translation table [search => [format, replace]] */
    private array $trTable = [];

    public function __construct(string $template)
    {
        $this->template = $template;
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
        $color = new Color;
        $colorStyles = [];
        foreach ($this->trTable as $search => $formatting) {
            $format = $formatting[0];
            if (!in_array($format, $colorStyles)) {
                array_push($colorStyles, $format);
                Color::style($format, self::CLI_TABLE[$format]);
            }
            $tr[$search] = $color->$format($formatting[1]);
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
            $tr[$search] = "<$tag>$replace</$tag>";
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

    /**
     * @codeCoverageIgnore
     */
    public function __toString(): string
    {
        return $this->toString();
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
        // if (strpos($this->template, $search) === false) {
        //     throw new MessageSearchNotExistsException(
        //         (new Message("Search string %search% doesn't exists in the template"))
        //             ->code('%search%', $search)
        //     );
        // }
        $new = clone $this;
        $new->trTable[$search] = [$format, $replace];

        return $new;
    }
}
