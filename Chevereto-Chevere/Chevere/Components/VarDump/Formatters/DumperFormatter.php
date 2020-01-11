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
use Chevere\Components\VarDump\Formatters\Traits\FilterEncodedCharsTrait;
use Chevere\Components\VarDump\Formatters\Traits\GetIndentTrait;
use Chevere\Components\VarDump\Contracts\FormatterContract;

/**
 * Provide Dumper VarDump representation (auto detect).
 */
final class DumperFormatter implements FormatterContract
{
    use GetIndentTrait;
    use FilterEncodedCharsTrait;

    private FormatterContract $formatter;

    public function __construct()
    {
        $this->formatter = BootstrapInstance::get()->isCli() ? new ConsoleFormatter() : new PlainFormatter();
    }

    public function applyWrap(string $key, string $dump): string
    {
        return $this->formatter->applyWrap(...func_get_args());
    }

    public function applyEmphasis(string $string): string
    {
        return $this->formatter->applyEmphasis(...func_get_args());
    }
}
