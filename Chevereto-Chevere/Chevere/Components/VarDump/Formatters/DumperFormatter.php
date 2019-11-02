<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Formatters;

use Chevere\Components\VarDump\Formatters\Traits\GetEmphasisTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\Components\VarDump\src\Wrapper;
use Chevere\Contracts\VarDump\FormatterContract;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

use const Chevere\CLI;

/**
 * Provide Dumper VarDump representation (auto detect).
 */
final class DumperFormatter implements FormatterContract
{
    use GetIndentTrait;
    use GetEncodedCharsTrait;

    public function wrap(string $key, string $dump): string
    {
        $wrapper = new Wrapper($key, $dump);
        if (CLI) {
            $wrapper = $wrapper->withCli();
        }

        return $wrapper->toString();
    }

    public function getEmphasis(string $string): string
    {
        if (!CLI) {
            return $string;
        }

        return (new ConsoleFormatter())
            ->getEmphasis($string);
    }
}
