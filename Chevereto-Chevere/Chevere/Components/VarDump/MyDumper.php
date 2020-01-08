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

namespace Chevere\Components\VarDump;

use Chevere\Components\VarDump\Formatters\HtmlFormatter as DumperFormatter;
use Chevere\Components\VarDump\Contracts\FormatterContract;

/**
 * A simple example in how you can extend Dumper and use your own FormatterContracr
 */
class MyDumper extends Dumper
{
    protected function getFormatter(): FormatterContract
    {
        return new DumperFormatter();
    }
}
