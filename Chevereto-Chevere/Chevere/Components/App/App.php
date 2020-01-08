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
use Chevere\Components\App\Contracts\AppContract;
use Chevere\Components\App\Contracts\ServicesContract;
use Chevere\Components\Http\Contracts\RequestContract;
use Chevere\Components\Http\Contracts\ResponseContract;
use Chevere\Components\Router\Contracts\RoutedContract;

/**
 * The application container.
 *
 * Provides access to the application, mostly intended for providing access at ControllerContract layer.
 */
final class App implements AppContract
{
    private ServicesContract $services;

    private ResponseContract $response;

    private RoutedContract $routed;

    private RequestContract $request;

    /** @var array String arguments (from request, cli) */
    private array $arguments;

    /**
     * {@inheritdoc}
     */
    public function __construct(ServicesContract $services, ResponseContract $response)
    {
        $this->services = $services;
        $this->response = $response;
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
    public function services(): ServicesContract
    {
        return $this->services;
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
    public function withRouted(RoutedContract $routed): AppContract
    {
        $new = clone $this;
        $new->routed = $routed;

        return $new;
    }

    public function hasRouted(): bool
    {
        return isset($this->routed);
    }

    public function routed(): RoutedContract
    {
        return $this->routed;
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
