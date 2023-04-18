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

namespace Chevere\Extra;

/**
 * Same as `array_filter` but filters recursively.
 *
 * @param array<mixed> $array
 * @return array<mixed>
 */
function array_filter_recursive(array $array, ?callable $callback = null, int $mode = 0): array
{
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = call_user_func(__FUNCTION__, $value, $callback, $mode);
        } elseif ($callback !== null) {
            $arguments = match ($mode) {
                ARRAY_FILTER_USE_KEY => [$key],
                ARRAY_FILTER_USE_BOTH => [$value, $key],
                default => [$value],
            };
            if (! $callback(...$arguments)) {
                $value = false;
            }
        }
        if (! $value) {
            unset($array[$key]);
        }
    }

    return $array;
}
