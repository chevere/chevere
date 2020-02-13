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

use Chevere\Components\Str\Interfaces\StrInterface;

/**
 * The Chevere mutable string.
 */
final class Str implements StrInterface
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public function lowercase(): StrInterface
    {
        $this->string = mb_strtolower($this->string, 'UTF-8');

        return $this;
    }

    public function stripWhitespace(): StrInterface
    {
        $this->string = preg_replace('/\s+/', '', $this->string) ?? '';

        return $this;
    }

    public function stripExtraWhitespace(): StrInterface
    {
        $this->string = preg_replace('/\s+/', ' ', $this->string) ?? '';

        return $this;
    }

    public function stripNonAlphanumerics(): StrInterface
    {
        $this->string = preg_replace('/[^[:alnum:]]/u', '', $this->string) ?? '';

        return $this;
    }

    public function forwardSlashes(): StrInterface
    {
        $this->string = str_replace('\\', '/', $this->string);

        return $this;
    }

    /**
     * Prepends a string with a tail string, without repeats.
     *
     * @param string $tail string tail
     */
    public function leftTail(string $tail): StrInterface
    {
        $this->string = $tail . ltrim($this->string, $tail);

        return $this;
    }

    /**
     * Appends a tail to string, without repeats.
     *
     * @param string $tail string tail
     */
    public function rightTail(string $tail): StrInterface
    {
        $this->string = rtrim($this->string, $tail) . $tail;

        return $this;
    }

    /**
     * Replace the first occurrence of the search string with the replacement
     * string.
     *
     * @param string $search  value being searched for
     * @param string $replace replacement value that replaces found search values
     */
    public function replaceFirst(string $search, string $replace): StrInterface
    {
        $pos = strpos($this->string, $search);
        if (false !== $pos) {
            $subject = substr_replace($this->string, $replace, $pos, mb_strlen($search));
        }
        $this->string = $subject ?? '';

        return $this;
    }

    /**
     * Replace the last occurrence of the search string with the replacement string.
     *
     * @param string $search  value being searched for
     * @param string $replace replacement value that replaces found search values
     */
    public function replaceLast(string $search, string $replace): StrInterface
    {
        $pos = strrpos($this->string, $search);
        if (false !== $pos) {
            $subject = substr_replace($this->string, $replace, $pos, mb_strlen($search));
        }

        $this->string = $subject ?? '';

        return $this;
    }

    /**
     * Removes CLI color format.
     */
    public function stripANSIColors(): StrInterface
    {
        $this->string = preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $this->string);

        return $this;
    }
}
