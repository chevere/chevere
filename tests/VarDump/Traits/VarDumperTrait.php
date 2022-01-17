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

use Chevere\Components\VarDump\Format\VarDumpPlainFormat;
use Chevere\Components\VarDump\VarDumpable;
use Chevere\Components\VarDump\VarDumper;
use function Chevere\Components\Writer\streamTemp;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Interfaces\VarDump\VarDumperInterface;

trait VarDumperTrait
{
    private function getVarDumper($var): VarDumperInterface
    {
        return new VarDumper(
            new StreamWriter(streamTemp('')),
            new VarDumpPlainFormat(),
            new VarDumpable($var)
        );
    }
}
