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

namespace Chevere\Components\ExceptionHandler\Interfaces;

use Chevere\Components\Common\Interfaces\ToArrayInterface;
use Chevere\Components\Common\Interfaces\ToStringInterface;
use Chevere\Components\VarDump\Interfaces\VarDumpInterface;

interface TraceInterface extends ToArrayInterface, ToStringInterface
{
    const HIGHLIGHT_TAGS = [
        '%file%' => VarDumpInterface::_FILE,
        '%line%' => VarDumpInterface::_FILE,
        '%fileLine%' => VarDumpInterface::_FILE,
        '%class%' => VarDumpInterface::_CLASS,
        '%type%' => VarDumpInterface::_OPERATOR,
        '%function%' => VarDumpInterface::_FUNCTION,
    ];
}
