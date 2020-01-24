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
    const TAG_ENTRY_FILE = '%file%';
    const TAG_ENTRY_LINE = '%line%';
    const TAG_ENTRY_FILE_LINE = '%fileLine%';
    const TAG_ENTRY_CLASS = '%class%';
    const TAG_ENTRY_TYPE = '%type%';
    const TAG_ENTRY_FUNCTION = '%function%';
    const TAG_ENTRY_CSS_EVEN_CLASS = '%cssEvenClass%';
    const TAG_ENTRY_POS = '%pos%';
    const TAG_ENTRY_ARGUMENTS = '%arguments%';

    const HIGHLIGHT_TAGS = [
        self::TAG_ENTRY_FILE => VarDumpInterface::_FILE,
        self::TAG_ENTRY_LINE => VarDumpInterface::_FILE,
        self::TAG_ENTRY_FILE_LINE => VarDumpInterface::_FILE,
        self::TAG_ENTRY_CLASS => VarDumpInterface::_CLASS,
        self::TAG_ENTRY_TYPE => VarDumpInterface::_OPERATOR,
        self::TAG_ENTRY_FUNCTION => VarDumpInterface::_FUNCTION,
    ];

    public function __construct(array $trace, FormatterInterface $formatter);
}
