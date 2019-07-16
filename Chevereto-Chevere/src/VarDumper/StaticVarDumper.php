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

use const Chevereto\Chevere\CLI;

/**
 * Analyze a variable and provide an output string representation of its type and data.
 */
abstract class StaticVarDumper
{
    /**
     * Wrap dump data HTML / CLI aware.
     *
     * @param string $key  Type or algo key (see constants)
     * @param mixed  $dump dump data
     *
     * @return string wrapped dump data
     */
    public static function wrap(string $key, string $dump): ?string
    {
        $wrapper = new Wrapper($key, $dump);
        if (CLI) {
            $wrapper->useCLI(true);
        }

        return $wrapper->toString();
    }

    public static function out($var, int $indent = null, array $dontDump = [], int $depth = 0): string
    {
        return (/* @scrutinizer ignore-call */ new static(...func_get_args()))->toString();
    }
}
