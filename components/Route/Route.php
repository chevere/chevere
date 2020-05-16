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
use Chevere\Components\Route\Interfaces\RouteEndpointsInterface;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use InvalidArgumentException;
use LogicException;
use OutOfBoundsException;

final class Route implements RouteInterface
{
    private RouteNameInterface $name;

    private RoutePathInterface $routePath;

    /** @var array details about the instance maker */
    private array $maker;

    /** @var array [wildcardName => $endpoint] */
    private array $wildcards;

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
     * @throws InvalidArgumentException If the controller doesn't take parameters
     * @throws OutOfBoundsException If wildcard binds to inexistent controller parameter name
     */
    public function withAddedEndpoint(RouteEndpointInterface $endpoint): RouteInterface
    {
        $new = clone $this;
        /** @var RouteWildcard $wildcard */
        foreach ($new->routePath->wildcards()->toArray() as $wildcard) {
            $new->assertWildcardEndpoint($wildcard, $endpoint);
            $wildcardMustRegex = $new->wildcards[$wildcard->name()] ?? null;
            $regex = $endpoint->controller()->parameters()
                ->get($wildcard->name())->regex();
            if (isset($wildcardMustRegex)) {
                if ($regex->toString() !== $wildcardMustRegex) {
                    throw new LogicException(
                        (new Message('Wildcard %wildcard% parameter regex %regex% (fist defined by %controller%) must be the same for all controllers in this route, regex %regexProvided% by %controllerProvided%'))
                            ->code('%wildcard%', $wildcard->toString())
                            ->code('%regex%', $wildcardMustRegex)
                            ->code('%controller%', $wildcard->toString())
                            ->code('%regexProvided%', $regex->toString())
                            ->code('%controllerProvided%', get_class($endpoint->controller()))
                    );
                }
            } else {
                $new->routePath = $new->routePath
                    ->withWildcard($wildcard->withMatch(
                        new RouteWildcardMatch($regex->toNoDelimitersNoAnchors())
                    ));
                $new->wildcards[$wildcard->name()] = $regex->toString();
            }
            $endpoint = $endpoint->withoutParameter($wildcard->name());
        }
        $new->endpoints->put($endpoint);

        return $new;
    }

    public function endpoints(): RouteEndpointsInterface
    {
        return $this->endpoints;
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

    /**
     * @throws InvalidArgumentException If the controller doesn't take parameters
     * @throws OutOfBoundsException If wildcard binds to inexistent controller parameter name
     */
    private function assertWildcardEndpoint(RouteWildcard $wildcard, RouteEndpoint $endpoint): void
    {
        if ($endpoint->controller()->parameters()->map()->count() === 0) {
            throw new InvalidArgumentException(
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
