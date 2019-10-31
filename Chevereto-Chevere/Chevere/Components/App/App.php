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

use Chevere\Components\App\Instances\RequestInstance;
use Chevere\Components\Route\Traits\RouteAccessTrait;
use Chevere\Components\Router\Traits\ServicesAccessTrait;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\ServicesContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

/**
 * The application container.
 *
 * Provides access to the application, mostly intended for providing access at ControllerContract layer.
 */
final class App implements AppContract
{
    use ServicesAccessTrait;
    use RouteAccessTrait;

    /** @var ResponseContract */
    private $response;

    /** @var RequestContract */
    private $request;

    /** @var array String arguments (from request, cli) */
    private $arguments;

    /**
     * {@inheritdoc}
     */
    public function __construct(ResponseContract $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function withResponse(ResponseContract $response): AppContract
    {
        $new = clone $this;
        $new->response = $response;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function response(): ResponseContract
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequest(RequestContract $request): AppContract
    {
        $new = clone $this;
        $new->request = $request;
        RequestInstance::set($request);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    /**
     * {@inheritdoc}
     */
    public function request(): RequestContract
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function withRoute(RouteContract $route): AppContract
    {
        $new = clone $this;
        $new->route = $route;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withServices(ServicesContract $services): AppContract
    {
        $new = clone $this;
        $new->services = $services;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withArguments(array $arguments): AppContract
    {
        $new = clone $this;
        $new->arguments = $arguments;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasArguments(): bool
    {
        return isset($this->arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function arguments(): array
    {
        return $this->arguments;
    }
}
