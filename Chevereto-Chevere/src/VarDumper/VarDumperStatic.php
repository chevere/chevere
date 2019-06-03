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

use JakubOnderka\PhpConsoleColor\ConsoleColor;

abstract class VarDumperStatic
{
    /**
     * Get color for palette key.
     *
     * @param string $key color palette key
     *
     * @return string color
     */
    public static function getColorForKey(string $key): ?string
    {
        return 'cli' == php_sapi_name() ? Pallete::CONSOLE[$key] ?? null : Pallete::PALETTE[$key] ?? null;
    }

    /**
     * Wrap dump data into HTML.
     *
     * @param string $key  Type or algo key (see constants)
     * @param mixed  $dump dump data
     *
     * @return string wrapped dump data
     */
    public static function wrap(string $key, $dump): ?string
    {
        $color = static::getColorForKey($key);
        if (isset($color)) {
            if ('cli' == php_sapi_name()) {
                $consoleColor = new ConsoleColor();

                return $consoleColor->apply($color, $dump);
            }

            return '<span style="color:'.$color.'">'.$dump.'</span>';
        } else {
            return (string) $dump;
        }
    }

    public static function out($var, int $indent = null, array $dontDump = [], int $depth = 0): string
    {
        return (string) new VarDumper(...func_get_args());
    }
}
