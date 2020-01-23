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

namespace Chevere\Components\Screen;

use Chevere\Components\Screen\Interfaces\ContainerInterface;
use Chevere\Components\Screen\Interfaces\ScreenInterface;

final class Container implements ContainerInterface
{
    private ScreenInterface $runtime;

    private ScreenInterface $debug;

    public function __construct(ScreenInterface $runtime, ScreenInterface $debug)
    {
        $this->runtime = $runtime;
        $this->debug = $debug;
    }

    public function runtime(): ScreenInterface
    {
        return $this->runtime;
    }

    public function debug(): ScreenInterface
    {
        return $this->debug;
    }
}
