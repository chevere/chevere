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

namespace Chevere\Interfaces\Routing;

use Chevere\Interfaces\Filesystem\DirInterface;

interface FsRoutesMakerInterface
{
    const ROUTE_NAME_BASENAME = 'RouteName.php';

    public function __construct(DirInterface $dir);

    public function fsRoutes(): FsRoutesInterface;
}
