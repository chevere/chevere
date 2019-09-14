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

use LogicException;

use const Chevere\APP_PATH;
use Chevere\Contracts\Api\ApiContract;
use Chevere\Message;
use Chevere\Contracts\App\AppContract;
use Chevere\Controller\ArgumentsWrap;
use Chevere\Http\Response;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Handler;
use Chevere\Http\Request;

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

    /** @var ApiContract */
    private $api;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /** @var array String arguments (from request uri, cli) */
    private $arguments;

    /** @var RouteContract */
    private $route;

    /** @var RouterContract */
    private $router;

    public function setApi(ApiContract $api): void
    {
        $this->api = $api;
    }

    public function setRequest(RequestContract $request): void
    {
        $this->request = $request;
    }

    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    public function setResponse(ResponseContract $response): void
    {
        $this->response = $response;
    }

    public function setRoute(RouteContract $route): void
    {
        $this->route = $route;
    }

    public function setRouter(RouterContract $router): void
    {
        $this->router = $router;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function api(): ApiContract
    {
        return $this->api;
    }

    public function request(): RequestContract
    {
        return $this->request;
    }

    public function response(): Response
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
                (new Message('Controller %s must implement the %c contract.'))
                    ->code('%s', $controller)
                    ->code('%i', ControllerContract::class)
                    ->toString()
            );
        }

        $middlewares = $this->route->middlewares();
        if (!empty($middlewares)) {
            $handler = new Handler($middlewares, $this);
            $handler->runner();
            if ($handler->exception) {
                dd($handler->exception->getMessage(), 'Aborted at ' . __FILE__ . ':' . __LINE__);
            }
        }

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
}
