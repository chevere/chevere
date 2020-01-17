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

namespace Chevere\Components\ExceptionHandler\Formatters;

use Chevere\Components\VarDump\Interfaces\FormatterInterface as VarDumpFormatterInterface;
use Chevere\Components\VarDump\Formatters\PlainFormatter as VarDumpFormatter;

final class PlainFormatter extends AbstractFormatter
{
    public function getVarDumpFormatter(): VarDumpFormatterInterface
    {
        return new VarDumpFormatter;
    }
}
