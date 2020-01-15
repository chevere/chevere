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
use Chevere\Components\VarDump\Interfaces\FormatterInterface;

/**
 * Provide Dumper VarDump representation (auto detect).
 */
final class DumperFormatter implements FormatterInterface
{
    use GetIndentTrait;
    use FilterEncodedCharsTrait;

    private FormatterInterface $formatter;

    public function __construct()
    {
        $this->formatter = BootstrapInstance::get()->isCli() ? new ConsoleFormatter() : new HtmlFormatter();
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
