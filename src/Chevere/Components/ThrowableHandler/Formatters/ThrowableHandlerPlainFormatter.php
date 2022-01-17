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

namespace Chevere\Components\ThrowableHandler\Formatters;

use Chevere\Components\VarDump\Format\VarDumpPlainFormat as VarDumpFormatter;
use Chevere\Interfaces\VarDump\VarDumpFormatInterface;

final class ThrowableHandlerPlainFormatter extends ThrowableHandlerFormatter
{
    public function getVarDumpFormatter(): VarDumpFormatInterface
    {
        return new VarDumpFormatter();
    }
}
