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

namespace Chevere\Interfaces\ThrowableHandler;

use Chevere\Interfaces\To\ToArrayInterface;
use Chevere\Interfaces\To\ToStringInterface;
use Chevere\Interfaces\VarDump\VarDumperInterface;

/**
 * Describes the component in charge of formatting a throwable trace entry.
 */
interface ThrowableTraceFormatterInterface extends ToArrayInterface, ToStringInterface
{
    public const TAG_ENTRY_FILE = '%file%';

    public const TAG_ENTRY_LINE = '%line%';

    public const TAG_ENTRY_FILE_LINE = '%fileLine%';

    public const TAG_ENTRY_CLASS = '%class%';

    public const TAG_ENTRY_TYPE = '%type%';

    public const TAG_ENTRY_FUNCTION = '%function%';

    public const TAG_ENTRY_CSS_EVEN_CLASS = '%cssEvenClass%';

    public const TAG_ENTRY_POS = '%pos%';

    public const HIGHLIGHT_TAGS = [
        self::TAG_ENTRY_FILE => VarDumperInterface::FILE,
        self::TAG_ENTRY_LINE => VarDumperInterface::FILE,
        self::TAG_ENTRY_FILE_LINE => VarDumperInterface::FILE,
        self::TAG_ENTRY_CLASS => VarDumperInterface::CLASS_REG,
        self::TAG_ENTRY_TYPE => VarDumperInterface::OPERATOR,
        self::TAG_ENTRY_FUNCTION => VarDumperInterface::FUNCTION,
    ];

    public function __construct(array $trace, ThrowableHandlerFormatterInterface $formatter);
}
