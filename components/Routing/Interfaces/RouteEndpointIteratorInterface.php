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
use SplObjectStorage;

interface RouteEndpointIteratorInterface
{
    public function __construct(DirInterface $dir);

    /**
     * Provides access to the SplObjectStorage instance.
     *
     * @return SplObjectStorage EndpointInterface objects
     */
    public function objects(): SplObjectStorage;
}
