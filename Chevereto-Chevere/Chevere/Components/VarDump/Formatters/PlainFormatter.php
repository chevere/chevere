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

use Chevere\Components\VarDump\Formatters\Traits\GetEmphasisTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\Components\VarDump\Contracts\FormatterContract;

/**
 * Provide plain text VarDump representation.
 */
final class PlainFormatter implements FormatterContract
{
    use GetIndentTrait;
    use GetEmphasisTrait;
    use GetEncodedCharsTrait;

    public function getWrap(string $key, string $dump): string
    {
        return $dump;
    }
}
