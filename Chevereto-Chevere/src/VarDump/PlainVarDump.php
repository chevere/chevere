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

namespace Chevere\VarDump;

/**
 * Dumps information about a variable in plain text format.
 */
class PlainVarDump extends ConsoleVarDump
{
    public static function wrap(string $key, string $dump): ?string
    {
        return $dump;
    }
}
