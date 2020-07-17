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

use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Http\MethodNotAllowedException;
use Chevere\Exceptions\Router\RouteNotFoundException;
use FastRoute\RouteCollector;

/**
 * Describes the component in charge of dispatch router.
 */
interface RouterDispatcherInterface
{
    public function __construct(RouteCollector $routeCollector);

    /**
     * Dispatches against the provided HTTP method verb and URI.
     *
     * @throws RouteNotFoundException
     * @throws MethodNotAllowedException
     * @throws LogicException if dispatcher returns an unexpected code.
     */
    public function dispatch(string $httpMethod, string $uri): RoutedInterface;
}
