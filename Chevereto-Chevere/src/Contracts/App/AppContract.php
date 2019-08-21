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
use Chevere\Contracts\Route\RouteContract;
use Chevere\HttpFoundation\Response;

interface AppContract
{
    public function setResponse(Response $response): void;

    public function setRoute(RouteContract $route): void;

    public function setArguments(array $arguments): void;

    public function response(): Response;

    public function route(): RouteContract;

    public function arguments(): array;

    /**
     * Run a controller on top of the App.
     *
     * @param string $controller a ControllerContract controller name
     */
    public function run(string $controller): ControllerContract;
}
