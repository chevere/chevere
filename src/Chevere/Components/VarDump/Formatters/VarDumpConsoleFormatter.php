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
use Chevere\Components\VarDump\Highlighters\VarDumpConsoleHighlight;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Interfaces\VarDump\VarDumpFormatterInterface;

final class VarDumpConsoleFormatter implements VarDumpFormatterInterface
{
    use IndentTrait;
    use FilterEncodedCharsTrait;

    public function emphasis(string $string): string
    {
        return
            (new VarDumpConsoleHighlight(VarDumperInterface::EMPHASIS))
                ->highlight($string);
    }

    public function highlight(string $key, string $string): string
    {
        return
            (new VarDumpConsoleHighlight($key))
                ->highlight($string);
    }
}
