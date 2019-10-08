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
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

interface AppContract
{
    public function __construct(ResponseContract $response);

    public function withResponse(ResponseContract $response): AppContract;

    public function withRoute(RouteContract $route): AppContract;

    public function withRouter(RouterContract $router): AppContract;

    public function withArguments(array $arguments): AppContract;

    public function response(): ResponseContract;

    public function route(): RouteContract;

    public function router(): RouterContract;

    public function arguments(): array;
}
