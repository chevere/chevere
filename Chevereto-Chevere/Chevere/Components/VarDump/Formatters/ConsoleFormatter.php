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

use Chevere\Components\VarDump\Formatters\Traits\GetEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\Components\VarDump\src\Wrapper;
use Chevere\Components\VarDump\Contracts\FormatterContract;
use JakubOnderka\PhpConsoleColor\ConsoleColor;

/**
 * Provide console VarDump representation.
 */
final class ConsoleFormatter implements FormatterContract
{
    use GetIndentTrait;
    use GetEncodedCharsTrait;

    public function wrap(string $key, string $dump): string
    {
        $wrapper = new Wrapper($key, $dump);
        $wrapper = $wrapper->withCli();

        return $wrapper->toString();
    }

    public function getEmphasis(string $string): string
    {
        $consoleColor = new ConsoleColor();

        return $consoleColor->apply(['color_245', 'italic'], $string);
    }
}
