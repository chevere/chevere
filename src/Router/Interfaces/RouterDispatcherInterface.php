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

namespace Chevere\Router\Interfaces;

use Chevere\Http\Exceptions\HttpMethodNotAllowedException;
use Chevere\Router\Exceptions\RouteNotFoundException;
use Chevere\Throwable\Exceptions\LogicException;
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
     * @throws HttpMethodNotAllowedException
     * @throws LogicException if dispatcher returns an unexpected code.
     */
    public function dispatch(string $httpMethod, string $uri): RoutedInterface;
}
