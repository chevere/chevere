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

namespace ChevereFn;

/**
 * Returns bytes from size + suffix format.
 *
 * @param string $size bytes to be formatted, like "2 MB"
 *
 * @return int byte representation
 */
// function bytesFromString(string $size): int
// {
//     $suffix = strtoupper(substr($size, -2));
//     if (strlen($suffix) == 1) {
//         $iniToSuffix = ['M' => 'MB', 'G' => 'GB'];
//         $suffix = $iniToSuffix[$suffix];
//     }
//     $value = intval($size);
//     if (!in_array($suffix, BYTES_UNITS)) {
//         return $value;
//     }

//     $array_search = array_search($suffix, BYTES_UNITS);
//     if (false !== $array_search) {
//         $powFactor = (int) $array_search + 1;
//     } else {
//         $powFactor = 0;
//     }

//     return (int) ($value * pow(1000, $powFactor));
// }

/**
 * Get bytes from the php.ini values.
 *
 * Allows to get a byte representation from php.ini values which uses short format notation.
 *
 * @param string $size short size notation (like "2M")
 *
 * @return float byte representation
 */
// function getIni(string $size): float
// {
// }
