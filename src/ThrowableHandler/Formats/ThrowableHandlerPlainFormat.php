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

namespace Chevere\ThrowableHandler\Formats;

use Chevere\VarDump\Formats\VarDumpPlainFormat;
use Chevere\VarDump\Interfaces\VarDumpFormatInterface;

final class ThrowableHandlerPlainFormat extends ThrowableHandlerFormat
{
    public function getVarDumpFormat(): VarDumpFormatInterface
    {
        return new VarDumpPlainFormat();
    }
}
