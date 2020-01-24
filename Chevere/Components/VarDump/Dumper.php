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
use Chevere\Components\VarDump\Dumpers\ConsoleDumper;
use Chevere\Components\VarDump\Dumpers\HtmlDumper;

/**
 * Context-aware dumper.
 */
final class Dumper implements ToStringInterface
{
    private string $dumped;

    public function __construct(...$vars)
    {
        $dumper = BootstrapInstance::get()->isCli() ? new ConsoleDumper() : new HtmlDumper();
        $this->dumped = $dumper
            ->withVars(...$vars)
            ->toString();
    }

    public function toString(): string
    {
        return $this->dumped;
    }

    public function toScreen(): void
    {
        screens()->runtime()->attachNl($this->dumped)->show();
    }
}
