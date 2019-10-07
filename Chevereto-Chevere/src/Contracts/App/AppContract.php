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

namespace Chevere\Contracts\App;

use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

interface AppContract
{
    /**
     * Construct an instance with the specific response.
     */
    public function __construct(ResponseContract $response);

    /**
     * Returns an instance with the specific request.
     */
    public function withRequest(RequestContract $request): AppContract;

    /**
     * Returns an instance with the specific response.
     */
    public function withResponse(ResponseContract $response): AppContract;

    /**
     * Returns an instance with the specific route.
     */
    public function withRoute(RouteContract $route): AppContract;

    /**
     * Returns an instance with the specific router.
     */
    public function withRouter(RouterContract $router): AppContract;

    /**
     * Returns an instance with the specific arguments.
     */
    public function withArguments(array $arguments): AppContract;

    public function hasRequest(): bool;

    public function response(): ResponseContract;

    public function request(): RequestContract;

    public function route(): RouteContract;

    public function router(): RouterContract;

    public function arguments(): array;

    /**
     * Run a controller on top of the App.
     *
     * @param string $controller a ControllerContract controller name
     */
    public function run(string $controller): ControllerContract;
}
