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

use Chevere\Interfaces\Str\StrInterface;

/**
 * The Chevere string manipulation.
 */
final class Str implements StrInterface
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function lowercase(): StrInterface
    {
        $new = clone $this;
        $new->string = mb_strtolower($new->string, 'UTF-8');

        return $new;
    }

    public function uppercase(): StrInterface
    {
        $new = clone $this;
        $new->string = mb_strtoupper($new->string, 'UTF-8');

        return $new;
    }

    public function stripWhitespace(): StrInterface
    {
        $new = clone $this;
        $new->string = preg_replace('/\s+/', '', $new->string) ?? '';

        return $new;
    }

    public function stripExtraWhitespace(): StrInterface
    {
        $new = clone $this;
        $new->string = preg_replace('/\s+/', ' ', $new->string) ?? '';

        return $new;
    }

    public function stripNonAlphanumerics(): StrInterface
    {
        $new = clone $this;
        $new->string = preg_replace('/[^[:alnum:]]/u', '', $new->string) ?? '';

        return $new;
    }

    public function forwardSlashes(): StrInterface
    {
        $new = clone $this;
        $new->string = str_replace('\\', '/', $new->string);

        return $new;
    }

    public function leftTail(string $tail): StrInterface
    {
        $new = clone $this;
        $new->string = $tail . ltrim($new->string, $tail);

        return $new;
    }

    public function rightTail(string $tail): StrInterface
    {
        $new = clone $this;
        $new->string = rtrim($new->string, $tail) . $tail;

        return $new;
    }

    public function replaceFirst(string $search, string $replace): StrInterface
    {
        $new = clone $this;
        $pos = strpos($new->string, $search);
        if (false !== $pos) {
            $subject = substr_replace($new->string, $replace, $pos, mb_strlen($search));
        }
        $new->string = $subject ?? '';

        return $new;
    }

    public function replaceLast(string $search, string $replace): StrInterface
    {
        $new = clone $this;
        $pos = strrpos($new->string, $search);
        if (false !== $pos) {
            $subject = substr_replace($new->string, $replace, $pos, mb_strlen($search));
        }
        $new->string = $subject ?? $new->string;

        return $new;
    }

    public function stripANSIColors(): StrInterface
    {
        $new = clone $this;
        $new->string = preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $new->string);

        return $new;
    }
}
