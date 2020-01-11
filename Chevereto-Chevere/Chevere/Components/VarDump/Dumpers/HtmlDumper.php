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
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;

final class HtmlDumper extends AbstractDumper
{
    public function getFormatter(): FormatterContract
    {
        return new HtmlFormatter();
    }

    public function getOutputter(): OutputterContract
    {
        return new HtmlOutputter();
    }
}
