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

namespace Chevere\Components\Routing\Interfaces;

use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Routing\RouteEndpointObjectsRead;
use SplObjectStorage;

interface RouteEndpointIteratorInterface
{
    public function __construct(DirInterface $dir);

    public function routeEndpointObjects(): RouteEndpointObjectsRead;
}
