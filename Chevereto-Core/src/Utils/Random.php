<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core\Utils;

class Random
{
    /**
     * Generate random numeric values within a limited range.
     *
     * @param int $min	 Minimum number.
     * @param int $max	 Maximum number.
     * @param int $limit Number of random values that should be generated.
     *
     * @return array An array with the generated random values.
     */
    public static function numericValues(int $min, int $max, int $limit) : array
    {
        // Get the accurate min and max
        $min = min($min, $max);
        $max = max($min, $max);
        // Go home
        if ($min == $max) {
            return [$min];
        }
        // is the limit ok?
        $maxLimit = abs($max - $min);
        if ($limit > $maxLimit) {
            $limit = $maxLimit;
        }
        $array = [];
        for ($i = 0; $i < $limit; $i++) {
            $rand = rand($min, $max);
            while (in_array($rand, $array)) {
                $rand = random_int($min, $max);
            }
            $array[$i] = $rand;
        }
        return $array;
    }
    /**
     * Generate a random string.
     *
     * @param int $length Length of the generated random string.
     *
     * @return string Random string.
     */
    public static function string(int $length=8) : string
    {
        $bytes = random_bytes($length);
        return bin2hex($bytes);
    }
}
