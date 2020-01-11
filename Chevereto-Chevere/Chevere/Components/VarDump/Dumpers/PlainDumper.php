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

namespace Chevere\Components\VarDump\Dumpers;

use Chevere\Components\VarDump\Contracts\FormatterContract;
use Chevere\Components\VarDump\Contracts\OutputterContract;
use Chevere\Components\VarDump\Dumper;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputter;

final class PlainDumper extends AbstractDumper
{
    public function getFormatter(): FormatterContract
    {
        return new PlainFormatter();
    }

    public function getOutputter(): OutputterContract
    {
        return new Outputter();
    }
}
