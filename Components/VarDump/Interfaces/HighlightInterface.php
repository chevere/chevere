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

namespace Chevere\Components\VarDump\Interfaces;

use Chevere\Components\Type\Interfaces\TypeInterface;

interface HighlightInterface
{
    const KEYS = [
        TypeInterface::STRING,
        TypeInterface::FLOAT,
        TypeInterface::INTEGER,
        TypeInterface::BOOLEAN,
        TypeInterface::NULL,
        TypeInterface::OBJECT,
        TypeInterface::ARRAY,
        TypeInterface::RESOURCE,
        VarDumperInterface::_FILE,
        VarDumperInterface::_CLASS,
        VarDumperInterface::_OPERATOR,
        VarDumperInterface::_FUNCTION,
        VarDumperInterface::_MODIFIERS,
        VarDumperInterface::_VARIABLE,
        VarDumperInterface::_EMPHASIS,
    ];

    public function __construct(string $key);

    public function wrap(string $dump): string;
}
