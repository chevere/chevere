<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\VarDump\Formatters;

use const Chevere\CLI;

use Chevere\Contracts\VarDump\FormatterContract;
use Chevere\VarDump\Formatters\Traits\GetEmphasisTrait;
use Chevere\VarDump\Formatters\Traits\GetEncodedCharsTrait;
use Chevere\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\VarDump\src\Wrapper;

/**
 * Provide Dumper VarDump representation (auto detect).
 */
final class DumperFormatter implements FormatterContract
{

    use GetIndentTrait;
    use GetEmphasisTrait;
    use GetEncodedCharsTrait;

    public function wrap(string $key, string $dump): string
    {
        $wrapper = new Wrapper($key, $dump);
        if (CLI) {
            $wrapper = $wrapper->withCli();
        }

        return $wrapper->toString();
    }
}
