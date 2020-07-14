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

use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Filesystem\DirInterface;

/**
 * Describes the component in charge of generating routing descriptors from a given directory.
 */
interface RoutingDescriptorsMakerInterface
{
    const ROUTE_NAME_BASENAME = 'RouteName.php';

    /**
     * @throws LogicException
     */
    public function __construct(DirInterface $dir);

    public function descriptors(): RoutingDescriptorsInterface;
}
