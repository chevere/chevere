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

namespace Chevere\Components\Str;

use Chevere\Interfaces\Str\StrBoolInterface;

final class StrBool implements StrBoolInterface
{
    public function __construct(
        private string $string
    ) {
    }

    public function empty(): bool
    {
        return $this->string === '';
    }

    public function ctypeSpace(): bool
    {
        return ctype_space($this->string) === true;
    }

    public function ctypeDigit(): bool
    {
        return ctype_digit($this->string) === true;
    }

    public function startsWithCtypeDigit(): bool
    {
        return strlen($this->string) > 0 && ctype_digit(mb_substr($this->string, 0, 1));
    }

    public function startsWith(string $needle): bool
    {
        return str_starts_with($this->string, $needle);
    }

    public function endsWith(string $needle): bool
    {
        return str_ends_with($this->string, $needle);
    }

    public function same(string $string): bool
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
