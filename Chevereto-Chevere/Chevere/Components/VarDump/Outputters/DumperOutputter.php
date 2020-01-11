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

namespace Chevere\Components\VarDump\Outputters;

use Chevere\Components\VarDump\Contracts\OutputterContract;
use Chevere\Components\VarDump\Dumpers\ConsoleDumper;
use Chevere\Components\VarDump\Dumpers\HtmlDumper;
use Chevere\Components\VarDump\Outputter;

final class DumperOutputter extends Outputter
{
    private OutputterContract $outputter;

    public function prepare(): OutputterContract
    {
        $outputter = $this->dumper->isCli() ? new ConsoleOutputter() : new HtmlOutputter();
        $outputter = $outputter
            ->withDumper($this->dumper);

        $this->dumper = $this->dumper
            ->withOutputter($outputter);

        return $this;
    }

    public function printOutput(): void
    {
        $this->dumper->outputter()->printOutput();
    }
}
