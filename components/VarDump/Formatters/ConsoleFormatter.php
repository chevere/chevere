<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\VarDump\Formatters;

use Chevere\Components\VarDump\Formatters\Traits\FilterEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\IndentTrait;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Highlighters\ConsoleHighlight;

/**
 * Provide console VarDump representation.
 */
final class ConsoleFormatter implements FormatterInterface
{
    use IndentTrait;
    use FilterEncodedCharsTrait;

    public function emphasis(string $string): string
    {
        return
            (new ConsoleHighlight(VarDumperInterface::_EMPHASIS))
                ->wrap($string);
    }

    public function highlight(string $key, string $string): string
    {
        return
            (new ConsoleHighlight($key))
                ->wrap($string);
    }
}
