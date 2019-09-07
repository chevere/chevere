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

namespace Chevere;

class Validate
{
    /**
     * Fast way to check for valid timezone.
     *
     * @param string $timezone timezone id
     *
     * @return bool TRUE if $timezone is a valid timezone
     */
    public static function timezone(string $timezone): bool
    {
        $return = false;
        $list = timezone_abbreviations_list();
        foreach ($list as $zone) {
            foreach ($zone as $item) {
                $tz = $item['timezone_id'] ?? null;
                if (isset($tz) && $timezone == $tz) {
                    $return = true;
                    break 2;
                }
            }
        }

        return $return;
    }

    /**
     * Checks if a regular expression pattern is valid.
     *
     * @param string $regex regular expresion pattern
     *
     * @return bool TRUE if $regex is a valid regular expression
     */
    public static function regex(string $regex): bool
    {
        set_error_handler(function () { }, E_WARNING);
        $return = false !== preg_match($regex, '');
        restore_error_handler();

        return $return;
    }
}
