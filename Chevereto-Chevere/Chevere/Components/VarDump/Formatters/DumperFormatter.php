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

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\VarDump\Formatters\Traits\GetEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\Components\VarDump\Contracts\FormatterContract;
use Chevere\Components\VarDump\Wrapper;

/**
 * Provide Dumper VarDump representation (auto detect).
 */
final class DumperFormatter implements FormatterContract
{
    use GetIndentTrait;
    use GetEncodedCharsTrait;

    public function getWrap(string $key, string $dump): string
    {
        $wrapper = new Wrapper($key, $dump);
        if (BootstrapInstance::get()->isCli()) {
            $wrapper = $wrapper->withCli();
        }

        return $wrapper->toString();
    }

    public function getEmphasis(string $string): string
    {
        if (!BootstrapInstance::get()->isCli()) {
            return $string;
        }

        return (new ConsoleFormatter())
            ->getEmphasis($string);
    }
}
