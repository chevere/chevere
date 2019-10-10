<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChevereFn;

/**
 * Replace the last occurrence of the search string with the replacement
 * string.
 *
 * @param string $search  value being searched for
 * @param string $replace replacement value that replaces found search
 *                        values
 * @param string $subject string being searched and replaced on
 *
 * @return string returns a string with the replaced value
 */
function stringReplaceLast(string $search, string $replace, string $subject): string
{
    $pos = strrpos($subject, $search);
    if (false !== $pos) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject ?? '';
}

/**
 * Replace the first occurrence of the search string with the replacement
 * string.
 *
 * @param string $search  value being searched for
 * @param string $replace replacement value that replaces found search
 *                        values
 * @param string $subject string being searched and replaced on
 *
 * @return string returns a string with the replaced value
 */
function stringReplaceFirst(string $search, string $replace, string $subject): string
{
    $pos = strpos($subject, $search);
    if (false !== $pos) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject ?? '';
}

/**
 * Right-tail a string.
 *
 * Appends a tail to string, without repeats.
 *
 * @param string $string
 * @param string $tail   string tail
 *
 * @return string right-tailed string
 */
function stringRightTail(string $string, string $tail): string
{
    return rtrim($string, $tail) . $tail;
}

/**
 * Left-tail a string.
 *
 * Prepends a string with a tail string, without repeats.
 *
 * @param string $string
 * @param string $tail   string tail
 *
 * @return string right-tailed string
 */
function stringLeftTail(string $string, string $tail): string
{
    return $tail . ltrim($string, $tail);
}

/**
 * Converts backslash into forward slashes.
 *
 * @param string $var path which will get forward slashes
 */
function stringForwardSlashes(string $var): string
{
    return str_replace('\\', '/', $var);
}
