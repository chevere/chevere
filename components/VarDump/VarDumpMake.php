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

namespace Chevere\Components\VarDump;

use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;
use Chevere\Components\VarDump\Outputters\PlainOutputter;
use Chevere\Components\VarDump\VarDump;
use Chevere\Interfaces\VarDump\VarDumpInterface;
use Chevere\Interfaces\Writers\WriterInterface;

/**
 * @codeCoverageIgnore
 */
final class VarDumpMake
{
    public static function plain(WriterInterface $writer): VarDumpInterface
    {
        return
            new VarDump(
                $writer,
                new PlainFormatter,
                new PlainOutputter
            );
    }

    public static function console(WriterInterface $writer): VarDumpInterface
    {
        return
            new VarDump(
                $writer,
                new ConsoleFormatter,
                new ConsoleOutputter
            );
    }

    public static function html(WriterInterface $writer): VarDumpInterface
    {
        return
            new VarDump(
                $writer,
                new HtmlFormatter,
                new HtmlOutputter
            );
    }
}
