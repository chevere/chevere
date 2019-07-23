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
 * Analyze a variable and provide a CLI output string representation of its type and data.
 */
class ConsoleVarDump extends VarDump
{
    public static function wrap(string $key, string $dump): ?string
    {
        $wrapper = new Wrapper($key, $dump);
        $wrapper->useCli();

        return $wrapper->toString();
    }
}
