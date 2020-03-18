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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\DataStructures\SplObjectStorageRead;
use Chevere\Components\Spec\RouteEndpointSpec;

final class RouteEndpointSpecObjectsRead extends SplObjectStorageRead
{
    /**
     * @return RouteEndpointSpec
     */
    public function current(): RouteEndpointSpec
    {
        return $this->objects->current();
    }

    public function getInfo()
    {
        return $this->objects->getInfo();
    }
}
