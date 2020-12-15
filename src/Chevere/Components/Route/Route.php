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
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Route\RouteEndpointConflictException;
use Chevere\Exceptions\Route\RouteWildcardConflictException;
use Chevere\Interfaces\Parameter\StringParameterInterface;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteEndpointsInterface;
use Chevere\Interfaces\Route\RouteInterface;
use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Interfaces\Route\RoutePathInterface;
use Chevere\Interfaces\Route\RouteWildcardInterface;

final class Route implements RouteInterface
{
    private RouteNameInterface $name;

    private RoutePathInterface $routePath;

    /** @var array details about the instance maker */
    private array $maker;

    /** @var array [wildcardName => $endpoint] */
    private array $wildcards;

    private RouteEndpointInterface $firstEndpoint;

    private RouteEndpointsInterface $endpoints;

    public function __construct(RoutePathInterface $routePath)
    {
        $this->name = $name;
        $this->routePath = $routePath;
        $this->maker = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        $this->endpoints = new RouteEndpoints();
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

    public function withAddedEndpoint(RouteEndpointInterface $endpoint): RouteInterface
    {
        $new = clone $this;
        $new->assertUnique($endpoint);
        $new->assertNoConflict($endpoint);
        foreach ($new->routePath->wildcards()->getGenerator() as $wildcard) {
            $new->assertWildcardEndpoint($wildcard, $endpoint);
            $knownWildcardMatch = $new->wildcards[$wildcard->toString()] ?? null;
            /** @var StringParameterInterface $controllerParamMatch */
            $controllerParamMatch = $endpoint->controller()->parameters()->get($wildcard->toString());
            $controllerParamMatch = $controllerParamMatch->regex()->toNoDelimitersNoAnchors();
            if (!isset($knownWildcardMatch)) {
                if ($controllerParamMatch !== $wildcard->match()->toString()) {
                    throw new RouteWildcardConflictException(
                        (new Message('Wildcard %parameter% matches against %match% which is incompatible with the match %controllerMatch% defined for %controller%'))
                            ->code('%parameter%', $wildcard->toString())
                            ->code('%match%', $wildcard->match()->toString())
                            ->code('%controllerMatch%', $controllerParamMatch)
                            ->code('%controller%', get_class($endpoint->controller()))
                    );
                }
                $new->wildcards[$wildcard->toString()] = $controllerParamMatch;
            }
            $endpoint = $endpoint->withoutParameter($wildcard->toString());
        }
        $new->endpoints = $new->endpoints->withPut($endpoint);

        return $new;
    }

    public function endpoints(): RouteEndpointsInterface
    {
        return $this->endpoints;
    }

    private function assertUnique(RouteEndpointInterface $endpoint): void
    {
        $key = $endpoint->method()->name();
        if ($this->endpoints->hasKey($key)) {
            throw new OverflowException(
                (new Message('Endpoint for method %method% has been already added'))
                    ->code('%method%', $key)
            );
        }
    }

    private function assertNoConflict(RouteEndpointInterface $endpoint): void
    {
        if (!isset($this->firstEndpoint)) {
            $this->firstEndpoint = $endpoint;
        } else {
            foreach ($this->firstEndpoint->parameters() as $name => $parameter) {
                if ($parameter['regex'] !== $endpoint->parameters()[$name]['regex']) {
                    throw new RouteEndpointConflictException(
                        (new Message('Controller parameters provided by %provided% must be compatible with the parameters defined first by %defined%'))
                            ->code('%provided%', get_class($endpoint->controller()))
                            ->code('%defined%', get_class($this->firstEndpoint->controller()))
                    );
                }
            }
        }
    }

    private function assertWildcardEndpoint(RouteWildcardInterface $wildcard, RouteEndpointInterface $endpoint): void
    {
        if ($endpoint->controller()->parameters()->count() === 0) {
            throw new InvalidArgumentException(
                (new Message("Controller %controller% doesn't accept any parameter (route wildcard %wildcard%)"))
                    ->code('%controller%', get_class($endpoint->controller()))
                    ->code('%wildcard%', $wildcard->toString())
            );
        }
        if (array_key_exists($wildcard->toString(), $endpoint->parameters()) === false) {
            $parameters = array_keys($endpoint->parameters());

            throw new OutOfBoundsException(
                (new Message('Wildcard parameter %wildcard% must bind to one of the known %controller% parameters: %parameters%'))
                    ->code('%wildcard%', $wildcard->toString())
                    ->code('%controller%', get_class($endpoint->controller()))
                    ->code('%parameters%', implode(', ', $parameters))
            );
        }
    }
}
