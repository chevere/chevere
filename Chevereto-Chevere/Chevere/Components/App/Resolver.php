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

use Chevere\Components\App\Exceptions\ResolverException;
use Chevere\Components\App\Exceptions\RouterCantResolveException;
use Chevere\Components\App\Exceptions\RouterContractRequiredException;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Method;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

/**
 * Application resolver.
 */
final class Resolver
{
    /** @var BuilderContract */
    private $builder;

    public function __construct(BuilderContract $builder)
    {
        $this->builder = $builder;
        $this->assertHasRouter();
        $this->assertRouterCanResolve();
        $this->resolveController();
    }

    public function builder(): BuilderContract
    {
        return $this->builder;
    }

    private function assertHasRouter(): void
    {
        if (!$this->builder->build()->app()->services()->hasRouter()) {
            throw new RouterContractRequiredException(
                (new Message('Instance of class %className% must contain a %contract% contract'))
                    ->code('%className%', get_class($this->builder->build()->app()))
                    ->code('%contract%', RouterContract::class)
                    ->toString()
            );
        }
    }

    private function assertRouterCanResolve(): void
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

    private function resolveController(): void
    {
        $app = $this->builder->build()->app();
        try {
            $routed = $app->services()->router()->resolve(
                $this->builder->build()->app()->request()->getUri()
            );
        } catch (RouteNotFoundException $e) {
            // HTTP 404: Not found
            throw new ResolverException($e->getMessage(), 404, $e);
        }
        $app = $app
            ->withRouted($routed);
        $collection = $routed->route()->methodControllerNameCollection();
        $requestMethod = new Method($app->request()->getMethod());
        try {
            $controllerName = $collection->get($requestMethod)->controllerName()->toString();
        } catch (MethodNotFoundException $e) {
            // HTTP 405: Method Not Allowed
            throw new ResolverException($e->getMessage(), 405, $e);
        }
        // TODO: controller arguments pass around app+builder
        $this->builder = $this->builder
            ->withControllerName($controllerName)
            ->withControllerArguments(
                $routed->wildcards()
            )
            ->withBuild(
                $this->builder->build()
                    ->withApp($app)
            );
    }
}
