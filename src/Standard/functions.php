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

use Chevere\Throwable\Exceptions\InvalidArgumentException;

function notEmpty(mixed $value): bool
{
    return ! empty($value);
}

/**
 * @phpstan-ignore-next-line
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
 * @phpstan-ignore-next-line
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
 * @phpstan-ignore-next-line
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

/**
 * @return array<int> Bits (powers of two)
 */
function getBits(int $value): array
{
    $return = [];
    $bit = 1;
    while ($bit <= $value) {
        if ($bit & $value) {
            $return[] = $bit;
        }
        $bit <<= 1;
    }

    return $return;
}

/**
 * @param string|int $key The key(s) to change (name: change,)
 * @phpstan-ignore-next-line
 */
function arrayChangeKey(array $array, string|int ...$key): array
{
    foreach ($key as $name => $change) {
        $name = strval($name);
        if (! array_key_exists($name, $array)) {
            continue;
        }
        $array[$change] = $array[$name];
        unset($array[$name]);
    }

    return $array;
}

/**
 * @phpstan-ignore-next-line
 */
function arrayPrefixKeys(array $array, string|int $prefix): array
{
    if ($prefix === '') {
        return $array;
    }
    $return = [];
    foreach ($array as $key => $value) {
        $return[$prefix . $key] = $value;
        unset($array[$key]);
    }

    return $return;
}

/**
 * @param string|int $key Key(s) to unset.
 * @phpstan-ignore-next-line
 */
function arrayUnsetKey(array $array, string|int ...$key): array
{
    foreach ($key as $name) {
        if (array_key_exists($name, $array)) {
            unset($array[$name]);
        }
    }

    return $array;
}

/**
 * @param string|int $key Key(s) to take.
 * @phpstan-ignore-next-line
 */
function arrayFromKey(array $array, string|int ...$key): array
{
    $return = [];
    foreach ($key as $name) {
        if (array_key_exists($name, $array)) {
            $return[$name] = $array[$name];
        }
    }

    return $return;
}

/**
 * @phpstan-ignore-next-line
 */
function listPrefixValues(array $array, string|int|float $prefix): array
{
    if (! array_is_list($array)) {
        throw new InvalidArgumentException('Array is not a list');
    }

    return array_map(fn ($value) => $prefix . $value, $array);
}
