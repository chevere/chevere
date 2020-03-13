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

namespace Chevere\Components\Spec;

use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Spec\Interfaces\SpecInterface;

final class SpecMaker
{
    private SpecInterface $spec;

    public function __construct(
        RoutePathInterface $path,
        DirInterface $dir,
        RouterInterface $router
    ) {
        $this->spec = new Spec();
    }

    public function spec(): SpecInterface
    {
        return $this->spec;
    }
}
