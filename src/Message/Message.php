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
use Stringable;

final class Message implements MessageInterface
{
    /**
     * @var array<string, string>
     */
    private array $trTable = [];

    /**
     * @param string $template A message template, i.e: `Disk %foo% is %percent% full`
     */
    public function __construct(
        private string $template,
        float|int|string|Stringable ...$translate
    ) {
        $array = [];
        foreach ($translate as $key => $value) {
            $array["%{$key}%"] = strval($value);
        }
        $this->trTable = $array;
    }

    public function __toString(): string
    {
        return strtr($this->template, $this->trTable);
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
        return $this->__toString();
    }

    public function toHtml(): string
    {
        return $this->__toString();
    }
}
