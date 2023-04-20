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
 * @param array<mixed> $array
 * @return array<mixed>
 */
function arrayFilterBoth(array $array, ?callable $callback = null): array
{
    $callable = $callback ?? __NAMESPACE__ . '\notEmpty';
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = call_user_func(__FUNCTION__, $value, $callable);
        }
        if (! $callable($value, $key)) {
            unset($array[$key]);
        }
    }

    return $array;
}

/**
 * @param array<mixed> $array
 * @return array<mixed>
 */
function arrayFilterValue(array $array, ?callable $callback = null): array
{
    $callable = $callback ?? __NAMESPACE__ . '\notEmpty';
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = call_user_func(__FUNCTION__, $value, $callable);
        }
        $notEmptyArray = is_array($value) && $value !== [];
        $response = $callable($value) ?: $notEmptyArray;
        if (! $response) {
            unset($array[$key]);
        }
    }

    return $array;
}

/**
 * @param array<mixed> $array
 * @return array<mixed>
 */
function arrayFilterKey(array $array, ?callable $callback = null): array
{
    $callable = $callback ?? __NAMESPACE__ . '\notEmpty';
    foreach ($array as $key => &$value) {
        if (is_array($value)) {
            $value = call_user_func(__FUNCTION__, $value, $callable);
        }
        if (! $callable($key)) {
            unset($array[$key]);
        }
    }

    return $array;
}
