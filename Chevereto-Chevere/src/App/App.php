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

use const Chevere\APP_PATH;

use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

/**
 * The App container.
 */
final class App implements AppContract
{
    const NAMESPACES = ['App', 'Chevere'];
    const APP = 'app';
    const FILEHANDLE_CONFIG = ':config';
    const FILEHANDLE_PARAMETERS = ':parameters';
    const PATH_LOGS = APP_PATH . 'var/logs/';

    /** @var ResponseContract */
    private $response;

    /** @var array String arguments (from request uri, cli) */
    private $arguments;

    /** @var RouteContract */
    private $route;

    /** @var RouterContract */
    private $router;

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
    public function withRoute(RouteContract $route): AppContract
    {
        $new = clone $this;
        $new->route = $route;

        return $new;
    }

    public function hasRoute(): bool
    {
        return isset($this->route);
    }

    /**
     * {@inheritdoc}
     */
    public function withRouter(RouterContract $router): AppContract
    {
        $new = clone $this;
        $new->router = $router;

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

    public function hasArguments(): bool
    {
        return isset($this->arguments);
    }

    public function response(): ResponseContract
    {
        return $this->response;
    }

    /**
     * Provides access to the RouteContract object associated with the existent request
     */
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
        return $this->arguments ?? [];
    }
}
