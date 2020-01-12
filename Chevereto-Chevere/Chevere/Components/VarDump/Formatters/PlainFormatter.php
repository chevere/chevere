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

namespace Chevere\Components\VarDump\Formatters;

use Chevere\Components\VarDump\Formatters\Traits\FilterEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;

/**
 * Provide plain text VarDump representation.
 */
final class PlainFormatter implements FormatterInterface
{
    use GetIndentTrait;
    use FilterEncodedCharsTrait;

    public function applyWrap(string $key, string $dump): string
    {
        return $dump;
    }

    public function applyEmphasis(string $string): string
    {
        return $string;
    }
}
