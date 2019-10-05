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

use Chevere\Contracts\Api\ApiContract;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

interface AppContract
{
    public function withRequest(RequestContract $request): AppContract;

    public function withResponse(ResponseContract $response): AppContract;

    public function withRoute(RouteContract $route): AppContract;

    public function withRouter(RouterContract $router): AppContract;

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
