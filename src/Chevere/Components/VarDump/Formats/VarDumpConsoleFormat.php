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

namespace Chevere\Components\VarDump\Formats;

use Chevere\Components\VarDump\Formats\Traits\FilterEncodedCharsTrait;
use Chevere\Components\VarDump\Formats\Traits\IndentTrait;
use Chevere\Components\VarDump\Highlights\VarDumpConsoleHighlight;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpFormatInterface;

final class VarDumpConsoleFormat implements VarDumpFormatInterface
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
