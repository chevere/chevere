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

use const Chevere\CLI;

class VarDump extends VarDumpAbstract
{
    protected function setPrefix(): void
    {
        $this->prefix = str_repeat(' ', $this->indent);
    }

    protected function getEmphasis(string $string): string
    {
        return $string;
    }

    protected function filterChars(string $string): string
    {
        return $string;
    }

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
            $wrapper->useCli();
        }

        return $wrapper->toString();
    }

    /**
     * Provides VarDump*::out.
     */
    public static function out($var, int $indent = null, array $dontDump = [], int $depth = 0): string
    {
        return (/* @scrutinizer ignore-call */new static(...func_get_args()))->toString();
    }
}
