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
use Chevere\Components\VarDump\VarDumpable;
use Chevere\Components\VarDump\VarDumper;
use Chevere\Components\Writers\StreamWriter;
use Chevere\Interfaces\VarDump\VarDumperInterface;
use Laminas\Diactoros\StreamFactory;

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
