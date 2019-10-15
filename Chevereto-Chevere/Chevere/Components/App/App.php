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

use Chevere\Components\Http\Traits\ResponseAccessTrait;
use Chevere\Components\Route\Traits\RouteAccessTrait;
use Chevere\Components\Router\Traits\RouterAccessTrait;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

/**
 * The App container.
 */
final class App implements AppContract
{
    use ResponseAccessTrait;
    use RouterAccessTrait;
    use RouteAccessTrait;

    /** @var array String arguments (from request uri, cli) */
    private $arguments;

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

    public function hasArguments(): bool
    {
        return isset($this->arguments);
    }

    public function arguments(): array
    {
        return $this->arguments;
    }
}
