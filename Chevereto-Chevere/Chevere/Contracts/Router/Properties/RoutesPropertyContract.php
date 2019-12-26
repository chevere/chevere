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

namespace Chevere\Contracts\Router\Properties;

use Chevere\Contracts\ToArrayContract;
use Chevere\Components\Router\Exceptions\RouterPropertyException;

interface RoutesPropertyContract extends ToArrayContract
{
    /** @var string property name */
    const NAME = 'routes';

    /**
     * Creates a new instance.
     *
     * @param array RouteContract members (objects serialized) [(int)$id => RouteContract]
     *
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function __construct(array $routes);
}
