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
use Chevere\Components\VarDump\Formatters\HtmlFormatter as VarDumpFormatter;

final class HtmlFormatter implements FormatterInterface
{
    public function getVarDumpFormatter(): VarDumpFormatter
    {
        return new VarDumpFormatter;
    }

    public function getTraceEntryTemplate(): string
    {
        return "<pre class=\"%cssEvenClass%\">#%i% %fileLine%\n%class%%type%%function%()%arguments%</pre>";
    }

    public function getHr(): string
    {
        return '<div class="hr"><span>------------------------------------------------------------</span></div>';
    }
}
