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
use Chevere\VarDump\src\Wrapper;

/**
 * Analyze a variable and provide a CLI/HTML aware output string representation of its type and data.
 */
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

    public static function wrap(string $key, string $dump): ?string
    {
        $wrapper = new Wrapper($key, $dump);
        if (CLI) {
            $wrapper->useCli();
        }

        return $wrapper->toString();
    }

    public static function out($var, int $indent = null, array $dontDump = [], int $depth = 0): string
    {
        return (/* @scrutinizer ignore-call */new static(...func_get_args()))->toString();
    }
}
