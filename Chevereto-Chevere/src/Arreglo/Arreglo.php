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

namespace Chevere\Arreglo;

/**
 * Array transformation utils.
 */
final class Arreglo
{
    /** @var array */
    private $array;

    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * Get the array with selected keys.
     * 
     * @param array  $keys  array keys to filter [0, 'key', 'keyN',...]
     *
     * @return array the selected array
     */
    public function toFilterKeys(array $keys): array
    {
        $return = [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->array)) {
                continue;
            }
            $return[$key] = $this->array[$key];
        }

        return $return;
    }

    /**
     * Get the array with removed keys.
     * 
     * @param array  $keys keys to remove ([key1, keyB, keyN,...])
     *
     * @return array the array without the passed keys
     */
    public function toRemoveKeys(array $keys): array
    {
        $array = $this->array;
        foreach ($keys as $v) {
            unset($array[$v]);
        }

        return $array;
    }

    /**
     * UTF-8 encode (recursive).
     *
     * @return array UTF-8 encoded array
     */
    public function toUtf8Encode(): array
    {
        $array = $this->array;
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
    public function toRemoveEmpty(): array
    {
        $array = $this->array;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::toRemoveEmpty($array[$key]);
            }
            if (empty($array[$key])) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Find all combinations of sets in an array, also called the power set.
     * @see https://www.oreilly.com/library/view/php-cookbook/1565926811/ch04s25.html
     *
     * @return array power set
     */
    public function getPowerSet(): array
    {
        $sets = [[]];
        foreach ($this->array as $member) {
            foreach ($sets as $combination) {
                $set = array_merge($combination, [$member]);
                $sets[] = $set;
            }
        }

        return $sets;
    }

    /**
     * Same as getPowerSet but preserving the keys.
     *
     * @return array power set
     */
    public function getPowerSetStrict(): array
    {
        $sets = [array_fill(0, count($this->array), null)];
        foreach ($this->array as $k => $member) {
            foreach ($sets as $combination) {
                $set = $combination;
                $set[$k] = $member;
                ksort($set);
                $sets[] = $set;
            }
        }

        return $sets;
    }
}
