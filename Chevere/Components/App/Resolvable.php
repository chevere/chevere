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

namespace Chevere\Components\App;

use Chevere\Components\App\Exceptions\RequestRequiredException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\Message\Message;
use Chevere\Components\App\Interfaces\BuilderInterface;
use Chevere\Components\App\Interfaces\ResolvableInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Route\Interfaces\RouteInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;

/**
 * Determines if the builder can resolve the build request
 */
final class Resolvable implements ResolvableInterface
{
    private BuilderInterface $builder;

    /**
     * @throws RequestRequiredException if $builder lacks of a request
     * @throws RouterRequiredException if $builder lacks of a RouterInterface
     * @throws RouterCantResolveException if $builder RouterInterface lacks of routing
     */
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
        $this->assertHasRequest();
        $this->assertHasRouter();
        $this->assertCanResolve();
    }

    public function builder(): BuilderInterface
    {
        return $this->builder;
    }

    private function assertHasRequest(): void
    {
        if (!$this->builder->build()->app()->hasRequest()) {
            throw new RequestRequiredException(
                $this->getMissingInterfaceMessage(RequestInterface::class)
            );
        }
    }

    private function assertHasRouter(): void
    {
        if (!$this->builder->build()->app()->services()->hasRouter()) {
            throw new RouterRequiredException(
                $this->getMissingInterfaceMessage(RouterInterface::class)
            );
        }
    }

    private function assertCanResolve(): void
    {
        $router = $this->builder->build()->app()->services()->router();
        if (!$router->canResolve()) {
            throw new RouterCantResolveException(
                (new Message("Instance of %className% can't resolve a %contract% contract"))
                    ->code('%className%', get_class($router))
                    ->code('%contract%', RouteInterface::class)
                    ->toString(),
                500
            );
        }
    }

    private function getMissingInterfaceMessage(string $contractName): string
    {
        return (new Message('Instance of class %className% must contain a %contract% contract'))
            ->code('%className%', get_class($this->builder->build()->app()))
            ->code('%contract%', $contractName)
            ->toString();
    }
}
