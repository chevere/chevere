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

use LogicException;
use Chevere\Message\Message;
use Chevere\Contracts\App\AppContract;
use Chevere\Controller\ArgumentsWrap;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

/**
 * The app container.
 */
final class App implements AppContract
{
    const NAMESPACES = ['App', 'Chevere'];
    const APP = 'app';
    const FILEHANDLE_CONFIG = ':config';
    const FILEHANDLE_PARAMETERS = ':parameters';
    const PATH_LOGS = APP_PATH . 'var/logs/';

    /** @var RequestContract */
    private $request;

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
    public function withRequest(RequestContract $request): AppContract
    {
        $new = clone $this;
        $new->request = $request;
        return $new;
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

    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    public function request(): RequestContract
    {
        return $this->request;
    }

    public function response(): ResponseContract
    {
        return $this->response;
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
        return $this->arguments ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function run(string $controller): ControllerContract
    {
        if (!is_subclass_of($controller, ControllerContract::class)) {
            throw new LogicException(
                (new Message('Controller %controller% must implement the %contract% interface.'))
                    ->code('%controller%', $controller)
                    ->code('%contract%', ControllerContract::class)
                    ->toString()
            );
        }

        $this->handleRouteMiddleware();

        $controller = new $controller($this);

        if (isset($this->arguments)) {
            $wrap = new ArgumentsWrap($controller, $this->arguments);
            $controllerArguments = $wrap->typedArguments();
        } else {
            $controllerArguments = [];
        }

        $controller(...$controllerArguments);

        return $controller;
    }

    private function handleRouteMiddleware()
    {
        if (isset($this->route)) {
            $middlewares = $this->route->middlewares();
            if (!empty($middlewares)) {
                $handler = new MiddlewareHandler($middlewares, $this);
                $handler->runner();
                if ($handler->exception) {
                    dd($handler->exception->getMessage(), 'Aborted at ' . __FILE__ . ':' . __LINE__);
                }
            }
        }
    }
}
