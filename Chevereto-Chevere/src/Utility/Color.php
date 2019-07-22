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

// Hacer objeto servicio? new Color(string, Color::RGB)->toHex()
// Combo object + static

namespace Chevere\Utility;

use Chevere\Message;

abstract class Color
{
    /**
     * Converts HEX to RGB (color).
     * http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/.
     *
     * @param string $hex   hexadecimal color representation
     * @param bool   $array TRUE to get array return [R,G,B]
     *
     * @return mixed Array with RGB values [R,G,B] or string like rgb(R,G,B).
     *
     * TODO: typehint return
     */
    public static function hexToRgb(string $hex, bool $array = true)
    {
        $hex = str_replace('#', '', $hex);
        if (3 == strlen($hex)) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = [$r, $g, $b];

        return $array ? $rgb : ('rgb(' . implode(',', $rgb) . ')');
    }

    /**
     * Converts RGB to HEX (color).
     *
     * @param mixed $rgb RGB color representation array [R,G,B] or rgb(r,g,b)
     *
     * @return string HEX value including the number sign (#)
     */
    public static function rgbToHex($rgb): ?string
    {
        $type = gettype($rgb);
        switch ($type) {
            case 'string':
                $val = preg_replace('/[^\d,]/', '', $rgb);
                if (is_string($val)) {
                    $val = explode(',', $val);
                } else {
                    return null;
                }
                break;
            case 'array':
                $val = $rgb;
                break;
            default:
                throw new InvalidArgumentException(
                    (new Message('Only %s and %a types can be used with this function (type %t provided)'))
                        ->code('%s', 'string')
                        ->code('%a', 'array')
                        ->code('%t', $type)
                        ->toString()
                );
        }
        $hex = '#';
        $hex .= str_pad(dechex($val[0]), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($val[1]), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($val[2]), 2, '0', STR_PAD_LEFT);

        return $hex;
    }
}
