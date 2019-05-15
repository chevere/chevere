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

namespace Chevereto\Chevere\Utils;

class DumpPlain extends Dump
{
    public static function out($expression, int $indent = null, array $dontDump = [], $depth = 0): string
    {
        $return = parent::out(...func_get_args());

        return strip_tags($return);
    }

    public static function wrap(string $key, $dump = null): string
    {
        if (null === $dump) {
            $dump = $key;
        }

        return strip_tags($dump);
    }
}
