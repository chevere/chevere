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
use Chevere\Components\App\Interfaces\AppInterface;
use Chevere\Components\App\Interfaces\ServicesInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Http\Interfaces\ResponseInterface;
use Chevere\Components\Router\Interfaces\RoutedInterface;

/**
 * The application container.
 *
 * Provides access to the application, mostly intended for providing access at ControllerInterface layer.
 */
final class App implements AppInterface
{
    private ServicesInterface $services;

    private ResponseInterface $response;

    private RoutedInterface $routed;

    private RequestInterface $request;

    /** @var array String arguments (from request, cli) */
    private array $arguments;

    /**
     * Constructs the application container.
     */
    public function __construct(ServicesInterface $services, ResponseInterface $response)
    {
        $this->services = $services;
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function withServices(ServicesInterface $services): AppInterface
    {
        $new = clone $this;
        $new->services = $services;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function services(): ServicesInterface
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function withResponse(ResponseInterface $response): AppInterface
    {
        $new = clone $this;
        $new->response = $response;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function response(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequest(RequestInterface $request): AppInterface
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
    public function request(): RequestInterface
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function withRouted(RoutedInterface $routed): AppInterface
    {
        $new = clone $this;
        $new->routed = $routed;

        return $new;
    }

    public function hasRouted(): bool
    {
        return isset($this->routed);
    }

    public function routed(): RoutedInterface
    {
        return $this->routed;
    }

    /**
     * {@inheritdoc}
     */
    public function withArguments(array $arguments): AppInterface
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
