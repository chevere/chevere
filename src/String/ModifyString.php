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

use Chevere\String\Interfaces\ModifyStringInterface;

final class ModifyString implements ModifyStringInterface
{
    public function __construct(
        private string $string
    ) {
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public function withLowercase(): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = mb_strtolower($new->string, 'UTF-8');

        return $new;
    }

    public function withUppercase(): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = mb_strtoupper($new->string, 'UTF-8');

        return $new;
    }

    public function withStripWhitespace(): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = preg_replace('/\s+/', '', $new->string) ?? '';

        return $new;
    }

    public function withStripExtraWhitespace(): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = preg_replace('/\s+/', ' ', $new->string) ?? '';

        return $new;
    }

    public function withStripNonAlphanumerics(): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = preg_replace('/[^[:alnum:]]/u', '', $new->string) ?? '';

        return $new;
    }

    public function withForwardSlashes(): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = str_replace('\\', '/', $new->string);

        return $new;
    }

    public function withLeftTail(string $tail): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = $tail . ltrim($new->string, $tail);

        return $new;
    }

    public function withRightTail(string $tail): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = rtrim($new->string, $tail) . $tail;

        return $new;
    }

    public function withReplaceFirst(string $search, string $replace): ModifyStringInterface
    {
        $new = clone $this;
        $pos = strpos($new->string, $search);
        if ($pos !== false) {
            $subject = substr_replace($new->string, $replace, $pos, strlen($search));
            $new->string = $subject;
        }

        return $new;
    }

    public function withReplaceLast(string $search, string $replace): ModifyStringInterface
    {
        $new = clone $this;
        $pos = strrpos($new->string, $search);
        if ($pos !== false) {
            $subject = substr_replace($new->string, $replace, $pos, strlen($search));
            $new->string = $subject;
        }

        return $new;
    }

    public function withReplaceAll(string $search, string $replace): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = str_replace($search, $replace, $new->string);

        return $new;
    }

    public function withStripANSIColors(): ModifyStringInterface
    {
        $new = clone $this;
        $new->string = preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $new->string) ?? '';

        return $new;
    }
}
