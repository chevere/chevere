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

namespace Chevere\Components\VarDump\Dumpers;

use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\OutputterInterface;
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;

final class HtmlDumper extends AbstractDumper
{
    public function formatter(): FormatterInterface
    {
        return $this->formatter ??= new HtmlFormatter;
    }

    public function outputter(): OutputterInterface
    {
        return $this->outputter ??= new HtmlOutputter;
    }
}
