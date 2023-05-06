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

namespace Chevere\String;

use Chevere\String\Interfaces\StringValidateInterface;

final class StringValidate implements StringValidateInterface
{
    public function __construct(
        private string $string
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->string === '';
    }

    public function isCtypeSpace(): bool
    {
        return ctype_space($this->string);
    }

    public function isCtypeDigit(): bool
    {
        return ctype_digit($this->string);
    }

    /**
     * @infection-ignore-all
     */
    public function isStartingWithCtypeDigit(): bool
    {
        return strlen($this->string) > 0 && ctype_digit(mb_substr($this->string, 0, 1));
    }

    public function isStartingWith(string $needle): bool
    {
        return str_starts_with($this->string, $needle);
    }

    public function isEndingWith(string $needle): bool
    {
        return str_ends_with($this->string, $needle);
    }

    /**
     * @infection-ignore-all
     */
    public function isSame(string $string): bool
    {
        $safe = $this->string;
        $safe .= chr(0);
        $string .= chr(0);
        $safeLen = mb_strlen($safe);
        $userLen = mb_strlen($string);
        $result = $safeLen - $userLen;
        for ($i = 0; $i < $userLen; ++$i) {
            $result |= (ord($safe[$i % $safeLen]) ^ ord($string[$i]));
        }

        return $result === 0;
    }

    public function contains(string $string): bool
    {
        return str_contains($this->string, $string);
    }
}
