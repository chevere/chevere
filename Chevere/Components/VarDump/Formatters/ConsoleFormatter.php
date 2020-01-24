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

namespace Chevere\Components\VarDump\Formatters;

use Chevere\Components\VarDump\Formatters\Traits\FilterEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;
use Chevere\Components\VarDump\Interfaces\VarInfoInterface;
use Chevere\Components\VarDump\Wrappers\ConsoleWrapper;

/**
 * Provide console VarDump representation.
 */
final class ConsoleFormatter implements FormatterInterface
{
    use GetIndentTrait;
    use FilterEncodedCharsTrait;

    public function applyWrap(string $key, string $string): string
    {
        return
            (new ConsoleWrapper($key))
                ->wrap($string);
    }

    public function applyEmphasis(string $string): string
    {
        return
            (new ConsoleWrapper(VarInfoInterface::_EMPHASIS))
                ->wrap($string);
    }
}
