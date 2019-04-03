<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Utils;

/**
 * Array handling and transformation utils.
 */
class Arr
{
    const FILTER_EXCLUSION = 'filter_exclusion';
    const FILTER_REMOVE = 'filter_remove';
    const DEFAULT_FILTER = self::FILTER_EXCLUSION;

    /**
     * Filter array with another array.
     * Useful to quickly filter an array using another array as the filter.
     *
     * @param array  $array source array to be filtered (key => value)
     * @param array  $keys  array keys to filter (keys as comma-separated values)
     * @param string $mode  Mode to filter the array:
     *                      Utils\Arr::FILTER_EXCLUSION grabs filter values from source array.
     *                      Utils\Arr::FILTER_REMOVE removes filter values from the source array.
     *
     * @return array the filtered array
     */
    public static function filterArray(array $array, array $keys, $mode = self::FILTER_EXCLUSION): array
    {
        $return = [];
        foreach ($keys as $k => $v) {
            switch ($mode) {
                default:
                case static::DEFAULT_FILTER:
                    if (!array_key_exists($v, $array)) {
                        break;
                    }
                    $return[$v] = $array[$v];
                break;
                case static::FILTER_REMOVE:
                    unset($array[$v]);
                break;
            }
        }

        return $mode == static::DEFAULT_FILTER ? $return : $array;
    }

    /**
     * UTF-8 encode an array (recursive).
     *
     * @param array $array array to be encoded to UTF-8
     *
     * @return array UTF-8 encoded array
     */
    public static function utf8Encode(array &$array): array
    {
        array_walk_recursive($array, function (&$val) {
            $val = mb_convert_encoding($val, 'UTF-8', mb_detect_encoding($val));
        });

        return $array;
    }

    /**
     * Remove empty properties from an array (recursive).
     *
     * @param array $array array to be cleaned
     *
     * @return array an array without empty properties
     */
    public static function removeEmpty(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = static::removeEmpty($array[$key]);
            }
            if (empty($array[$key])) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Find all combinations of sets in an array, also called the power set.
     *
     * If you pass a subject, it enabled the generation for creating multiple sets from a subject string.
     *
     * @see https://www.oreilly.com/library/view/php-cookbook/1565926811/ch04s25.html
     *
     * @param array $array        array to determine its power set
     * @param bool  $preserveKeys true to preserve keys on power set
     *
     * @return array array power set
     */
    public static function powerSet(array $array, bool $preserveKeys = false): array
    {
        $sets = [];
        $sets[] = $preserveKeys ? array_fill(0, count($array), null) : [];
        foreach ($array as $k => $element) {
            foreach ($sets as $combination) {
                if ($preserveKeys) {
                    $set = $combination;
                    $set[$k] = $element;
                    ksort($set);
                } else {
                    $set = array_merge($combination, [$element]);
                }

                $sets[] = $set;
            }
        }

        return $sets;
    }
}
