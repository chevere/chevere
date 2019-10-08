<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\App;

use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Http\Traits\ResponseAccessTrait;

/**
 * The App container.
 */
final class App implements AppContract
{
    use ResponseAccessTrait;

    /** @var array String arguments (from request uri, cli) */
    private $arguments;

    /** @var RouteContract */
    private $route;

    /** @var RouterContract */
    private $router;

    public function __construct(ResponseContract $response)
    {
        $this->response = $response;
    }

    public function withResponse(ResponseContract $response): AppContract
    {
        $new = clone $this;
        $new->response = $response;

        return $new;
    }

    public function withRoute(RouteContract $route): AppContract
    {
        $new = clone $this;
        $new->route = $route;

        return $new;
    }

    public function withRouter(RouterContract $router): AppContract
    {
        $new = clone $this;
        $new->router = $router;

        return $new;
    }

    public function withArguments(array $arguments): AppContract
    {
        $new = clone $this;
        $new->arguments = $arguments;

        return $new;
    }

    public function hasRoute(): bool
    {
        return isset($this->route);
    }

    public function hasRouter(): bool
    {
        return isset($this->router);
    }

    public function hasArguments(): bool
    {
        return isset($this->arguments);
    }

    public function route(): RouteContract
    {
        return $this->route;
    }

    public function router(): RouterContract
    {
        return $this->router;
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
