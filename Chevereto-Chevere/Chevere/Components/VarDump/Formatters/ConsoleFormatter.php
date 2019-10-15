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
use Chevere\Components\VarDump\src\Wrapper;
use Chevere\Contracts\VarDump\FormatterContract;

/**
 * Provide console VarDump representation.
 */
final class ConsoleFormatter implements FormatterContract
{

    use GetIndentTrait;
    use GetEmphasisTrait;
    use GetEncodedCharsTrait;

    // Console 
    public function wrap(string $key, string $dump): string
    {
        $wrapper = new Wrapper($key, $dump);
        $wrapper = $wrapper->withCli();

        return $wrapper->toString();
    }
}
