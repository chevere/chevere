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
use Chevere\Exceptions\Core\RangeException;
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

    private RouteEndpoints $endpoints;

    public function __construct(RouteNameInterface $name, RoutePathInterface $routePath)
    {
        $this->name = $name;
        $this->routePath = $routePath;
        $this->maker = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0];
        $this->endpoints = new RouteEndpoints;
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
        if ($this->endpoints->hasKey($endpoint->method()->name())) {
            throw new OverflowException(
                (new Message('Endpoint for method %method% has been already added'))
                    ->code('%method%', $endpoint->method()->name())
            );
        }
        $new = clone $this;
        foreach ($new->routePath->wildcards()->getGenerator() as $wildcard) {
            $new->assertWildcardEndpoint($wildcard, $endpoint);
            $knownWildcardMatch = $new->wildcards[$wildcard->name()] ?? null;
            $controllerMatchString = $endpoint->controller()->parameters()
                ->get($wildcard->name())->regex()->toNoDelimitersNoAnchors();
            if (isset($knownWildcardMatch)) {
                if ($controllerMatchString !== $knownWildcardMatch) {
                    throw new RangeException(
                        (new Message('Wildcard %parameter% regex %regex% (fist defined by %controller%) must be the same for all controllers in this route, regex %regexProvided% by %controller%'))
                            ->code('%parameter%', $wildcard->name())
                            ->code('%regex%', $knownWildcardMatch)
                            ->code('%controller%', $wildcard->name())
                            ->code('%regexProvided%', $controllerMatchString)
                            ->code('%controller%', get_class($endpoint->controller()))
                    );
                }
            } else {
                if ($controllerMatchString !== $wildcard->match()->toString()) {
                    throw new RangeException(
                        (new Message('Wildcard %parameter% matches against %match% which is incompatible with the match %controllerMatch% defined for %controller%'))
                            ->code('%parameter%', $wildcard->name())
                            ->code('%match%', $wildcard->match()->toString())
                            ->code('%controllerMatch%', $controllerMatchString)
                            ->code('%controller%', get_class($endpoint->controller()))
                    );
                }
                $new->wildcards[$wildcard->name()] = $controllerMatchString;
            }
            // $endpoint = $endpoint->withoutParameter($wildcard->name());
        }
        $new->endpoints = $new->endpoints->withPut($endpoint);

        return $new;
    }

    public function endpoints(): RouteEndpointsInterface
    {
        return $this->endpoints;
    }

    /**
     * @throws InvalidArgumentException If the controller doesn't take parameters
     * @throws OutOfBoundsException If wildcard binds to inexistent controller parameter name
     */
    private function assertWildcardEndpoint(RouteWildcardInterface $wildcard, RouteEndpointInterface $endpoint): void
    {
        if ($endpoint->controller()->parameters()->count() === 0) {
            throw new InvalidArgumentException(
                (new Message("Controller %controller% doesn't accept any parameter (route wildcard %wildcard%)"))
                    ->code('%controller%', get_class($endpoint->controller()))
                    ->code('%wildcard%', $wildcard->name())
            );
        }
        if (array_key_exists($wildcard->name(), $endpoint->parameters()) === false) {
            $parameters = array_keys($endpoint->parameters());
            throw new OutOfBoundsException(
                (new Message('Wildcard parameter %wildcard% must bind to a one of the known %controller% parameters: %parameters%'))
                    ->code('%wildcard%', $wildcard->name())
                    ->code('%controller%', get_class($endpoint->controller()))
                    ->code('%parameters%', implode(', ', $parameters))
            );
        }
    }
}
