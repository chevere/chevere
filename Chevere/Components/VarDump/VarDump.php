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

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Common\Interfaces\ToStringInterface;
use Chevere\Components\VarDump\Formatters\ConsoleFormatter;
use Chevere\Components\VarDump\Formatters\HtmlFormatter;
use Chevere\Components\Screen\Interfaces\ScreenInterface;
use Chevere\Components\VarDump\Outputters\ConsoleOutputter;
use Chevere\Components\VarDump\Outputters\HtmlOutputter;

/**
 * The Chevere VarDump.
 * A context-aware VarDumper.
 */
final class VarDump implements ToStringInterface
{
    private string $dump;

    public function __construct(...$vars)
    {
        if (BootstrapInstance::get()->isCli()) {
            $formatter = ConsoleFormatter::class;
            $outputter = ConsoleOutputter::class;
        } else {
            $formatter = HtmlOutputter::class;
            $outputter = HtmlFormatter::class;
        }
        $dumper = new VarDumper(new $formatter, ...$vars);
        $this->dump = (new $outputter($dumper))->toString();
    }

    public function toString(): string
    {
        return $this->dump;
    }
}
