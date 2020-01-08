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

namespace Chevere\Components\Router\Contracts\Properties;

use Chevere\Contracts\ToArrayContract;
use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Route\Contracts\RouteContract;

interface RoutesPropertyContract extends ToArrayContract
{
    /** @var string property name */
    const NAME = 'routes';

    /**
     * Creates a new instance.
     *
     * @param array $routes [(int)$id => RouteContract]
     *
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function __construct(array $routes);
}
