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

namespace Chevere\Components\ExceptionHandler\Formatters;

use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter as VarDumpFormatter;

final class ConsoleFormatter implements FormatterInterface
{
    public function getVarDumpFormatter(): VarDumpFormatter
    {
        return new VarDumpFormatter;
    }

    public function getTraceEntryTemplate(): string
    {
        return "#%i% %fileLine%\n%class%%type%%function%()%arguments%";
    }

    public function getHr(): string
    {
        return '------------------------------------------------------------';
    }
}
