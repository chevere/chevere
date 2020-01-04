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

namespace Chevere\Components\App;

use Chevere\Components\App\Exceptions\RequestRequiredException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RouterRequiredException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\ResolvableContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

/**
 * Determines if the builder can resolve the build request
 */
final class Resolvable implements ResolvableContract
{
    private BuilderContract $builder;

    /**
     * {@inheritdoc}
     */
    public function __construct(BuilderContract $builder)
    {
        $this->builder = $builder;
        $this->assertHasRequest();
        $this->assertHasRouter();
        $this->assertCanResolve();
    }

    /**
     * {@inheritdoc}
     */
    public function builder(): BuilderContract
    {
        return $this->builder;
    }

    private function assertHasRequest(): void
    {
        if (!$this->builder->build()->app()->hasRequest()) {
            throw new RequestRequiredException(
                $this->getMissingContractMessage(RequestContract::class)
            );
        }
    }

    private function assertHasRouter(): void
    {
        if (!$this->builder->build()->app()->services()->hasRouter()) {
            throw new RouterRequiredException(
                $this->getMissingContractMessage(RouterContract::class)
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
                    ->code('%contract%', RouteContract::class)
                    ->toString(),
                500
            );
        }
    }

    private function getMissingContractMessage(string $contractName): string
    {
        return (new Message('Instance of class %className% must contain a %contract% contract'))
            ->code('%className%', get_class($this->builder->build()->app()))
            ->code('%contract%', $contractName)
            ->toString();
    }
}
