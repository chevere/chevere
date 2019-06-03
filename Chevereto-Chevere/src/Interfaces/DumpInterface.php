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

namespace Chevereto\Chevere\Interfaces;

interface DumpInterface
{
    /**
     * Dumps information about a variable.
     *
     * @param mixed $var      anything
     * @param int   $indent   left padding (spaces) for this entry
     * @param array $dontDump array containing classes that won't get dumped
     *
     * @return string parsed dump string
     */
    public static function out($var, int $indent = null, array $dontDump = [], int $depth = 0): string;
}
