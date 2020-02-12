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

namespace Chevere\Components\VarDump;

use Chevere\Components\VarDump\Interfaces\VarDumperInterface;
use Chevere\Components\VarDump\Interfaces\FormatterInterface;

final class VarDumper implements VarDumperInterface
{
    protected FormatterInterface $formatter;

    protected array $vars = [];

    protected array $debugBacktrace;

    public function __construct(array $debugBacktrace, FormatterInterface $formatter, ...$vars)
    {
        $this->debugBacktrace = $debugBacktrace;
        $this->vars = $vars;
        $this->formatter = $formatter;
    }

    public function formatter(): FormatterInterface
    {
        return $this->formatter;
    }

    public function vars(): array
    {
        return $this->vars;
    }

    public function debugBacktrace(): array
    {
        return $this->debugBacktrace;
    }
}
