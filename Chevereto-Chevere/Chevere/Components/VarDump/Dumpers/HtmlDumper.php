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
use Chevere\Components\VarDump\Formatters\HtmlFormatter;

final class HtmlDumper extends Dumper
{
    public function getFormatter(): FormatterContract
    {
        return new HtmlFormatter();
    }

    public function handleOutput(): void
    {
        return;
    }
}
