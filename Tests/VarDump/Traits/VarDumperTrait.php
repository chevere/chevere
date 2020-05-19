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

namespace Chevere\Tests\VarDump\Traits;

use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Interfaces\VarDump\FormatterInterface;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Chevere\Components\VarDump\VarDumpable;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Interfaces\Writers\WriterInterface;
use Chevere\Components\Writers\StreamWriter;
use GuzzleHttp\Psr7\BufferStream;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\StreamFactory;
use function GuzzleHttp\Psr7\stream_for;

trait VarDumperTrait
{
    private function getVarDumper($var): VarDumperInterface
    {
        return new VarDumper(
            new StreamWriter((new StreamFactory)->createStream('')),
            new PlainFormatter,
            new VarDumpable($var)
        );
    }
}
