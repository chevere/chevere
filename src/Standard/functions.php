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

namespace Chevere\Standard;

function notEmpty(mixed $value): bool
{
    return ! empty($value);
}

/**
 * Same as `array_filter` but filters recursively.
 *
 * @param array<mixed> $array
 * @return array<mixed>
 */
function arrayFilterRecursive(array $array, ?callable $callback = null, int $mode = 0): array
{
    $callable = $callback ?? __NAMESPACE__ . '\notEmpty';
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = call_user_func(__FUNCTION__, $value, $callable, $mode);
        }
        $arguments = match ($mode) {
            ARRAY_FILTER_USE_KEY => [$key],
            ARRAY_FILTER_USE_BOTH => [$value, $key],
            default => [$value],
        };
        $response = $callable(...$arguments);
        if (! $response && is_array($value) && $mode === 0) {
            $response = $value !== [];
        }
        if (! $response) {
            unset($array[$key]);
        }
    }

    return $array;
}
