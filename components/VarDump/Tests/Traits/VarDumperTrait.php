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

namespace Chevere\Components\VarDump\Tests\Traits;

use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\VarDumpeable;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Writers\Interfaces\WriterInterface;
use Chevere\Components\Writers\StreamWriter;
use GuzzleHttp\Psr7\BufferStream;
use function GuzzleHttp\Psr7\stream_for;

trait VarDumperTrait
{
    private function getVarDumper($var): VarDumperInterface
    {
        return new VarDumper(
            new StreamWriter(stream_for('')),
            new PlainFormatter,
            new VarDumpeable($var)
        );
    }
}
