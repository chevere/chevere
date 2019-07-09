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

namespace Chevereto\Chevere\VarDumper;

/**
 * Exactly the same as Dump but stripping any format decorators.
 */
class PlainVarDumper extends VarDumper
{
    public static function out($expression, int $indent = null, array $dontDump = [], $depth = 0): string
    {
        return strip_tags(parent::out(...func_get_args()));
    }

    public static function wrap(string $key, $dump): ?string
    {
        return strip_tags($dump);
    }
}
