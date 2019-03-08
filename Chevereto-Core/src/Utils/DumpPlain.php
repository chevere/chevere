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

class DumpPlain extends Dump
{
    public static function out($expression, int $indent = null) : string
    {
        $return = parent::out(...func_get_args());
        return strip_tags($return);
    }
    public static function wrap(string $key, $dump = null) : string
    {
        if ($dump === null) {
            $dump = $key;
        }
        return strip_tags($dump);
    }
}
