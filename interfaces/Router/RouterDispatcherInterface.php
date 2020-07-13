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

namespace Chevere\Interfaces\Router;

use Chevere\Exceptions\Router\RouterException;
use FastRoute\RouteCollector;

/**
 * Describes the component in charge of dispatch router.
 */
interface RouterDispatcherInterface
{
    public function __construct(RouteCollector $routeCollector);

    /**
     * @throws RouterException if the route is not found or the method not allowed
     * @throws LogicException
     */
    public function dispatch(string $httpMethod, string $uri): RoutedInterface;
}
