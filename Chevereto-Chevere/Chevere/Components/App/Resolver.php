<?php

namespace Chevere\Components\App;

use Chevere\Contracts\App\ResolverContract;
use Chevere\Components\App\Exceptions\ResolverException;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Http\Method;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\ResolvableContract;

/**
 * Resolves a builder against routing
 */
final class Resolver implements ResolverContract
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ResolvableContract $resolvable)
    {
        $this->builder = $resolvable->builder();
        $app = $this->builder->build()->app();
        try {
            $routed = $app->services()->router()->resolve(
                $app->request()->getUri()
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

    /**
     * {@inheritdoc}
     */
    public function builder(): BuilderContract
    {
        return $this->builder;
    }
}
