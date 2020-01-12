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

use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;
use Chevere\Components\VarDump\Formatters\PlainFormatter;
use Chevere\Components\VarDump\Outputters\PlainOutputter;

final class PlainDumper extends AbstractDumper
{
    public function getFormatter(): FormatterInterface
    {
        return new PlainFormatter();
    }

    public function getOutputter(): OutputterInterface
    {
        return new PlainOutputter();
    }
}
