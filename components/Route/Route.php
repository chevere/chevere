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

namespace Chevere\Components\Route;

use Chevere\Components\Message\Message;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;
use Chevere\Components\Middleware\MiddlewareNameCollection;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Router\Exceptions\RouterException;
use LogicException;
use OutOfBoundsException;
use function DeepCopy\deep_copy;

final class Route implements RouteInterface
{
    private RouteNameInterface $name;

    private RoutePathInterface $routePath;

    /** @var array An array containg details about the instance maker */
    private array $maker;

    private MiddlewareNameCollectionInterface $middlewareNameCollection;

    private RouteEndpoints $endpoints;

    public function __construct(RouteNameInterface $name, RoutePathInterface $routePath)
    {
        $this->name = $name;
        $this->routePath = $routePath;
        $this->maker = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        $this->endpoints = new RouteEndpoints;
        $this->middlewareNameCollection = new MiddlewareNameCollection;
    }

    public function name(): RouteNameInterface
    {
        return $this->name;
    }

    public function path(): RoutePathInterface
    {
        return $this->routePath;
    }

    public function maker(): array
    {
        return $this->maker;
    }

    /**
     * @throws LogicException If using a route wildcard but no parameter is accepted by the controller
     * @throws OutOfBoundsException If the route wildcard parameter is unknown for the controller
     */
    public function withAddedEndpoint(RouteEndpointInterface $endpoint): RouteInterface
    {
        $new = clone $this;
        /** @var RouteWildcard $wildcard */
        foreach ($new->routePath->wildcards()->toArray() as $wildcard) {
            $this->assertWildcardEndpoint($wildcard, $endpoint);
            $regex = $endpoint->controller()->parameters()->get($wildcard->name())->regex();
            $delimiter = $regex[0];
            $regex = trim($regex, $delimiter);
            $regex = preg_replace('#^\^(.*)\$$#', '$1', $regex);
            $new->routePath = $new->routePath
                ->withWildcard($wildcard->withMatch(
                    new RouteWildcardMatch($regex)
                ));
            $endpoint = $endpoint->withoutParameter($wildcard->name());
        }
        $new->endpoints->put($endpoint);

        return $new;
    }

    public function endpoints(): RouteEndpoints
    {
        return deep_copy($this->endpoints);
    }

    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): RouteInterface
    {
        $new = clone $this;
        $new->middlewareNameCollection = $new->middlewareNameCollection
            ->withAddedMiddlewareName($middlewareName);

        return $new;
    }

    public function middlewareNameCollection(): MiddlewareNameCollectionInterface
    {
        return $this->middlewareNameCollection;
    }

    private function assertWildcardEndpoint(RouteWildcard $wildcard, RouteEndpoint $endpoint): void
    {
        if ($endpoint->controller()->parameters()->map()->count() === 0) {
            throw new LogicException(
                (new Message("Controller %controller% doesn't accept any parameter (route wildcard %wildcard%)"))
                    ->code('%controller%', get_class($endpoint->controller()))
                    ->code('%wildcard%', $wildcard->toString())
                    ->toString()
            );
        }
        if (array_key_exists($wildcard->name(), $endpoint->parameters()) === false) {
            $parameters = array_keys($endpoint->parameters());
            throw new OutOfBoundsException(
                (new Message('Wildcard parameter %wildcard% must bind to a one of the known %controller% parameters: %parameters%'))
                    ->code('%wildcard%', $wildcard->toString())
                    ->code('%controller%', get_class($endpoint->controller()))
                    ->code('%parameters%', implode(', ', $parameters))
                    ->toString()
            );
        }
    }
}
