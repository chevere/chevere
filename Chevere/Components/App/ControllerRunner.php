<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App;

use Chevere\Components\App\Exceptions\ControllerInterfaceException;
use Chevere\Components\App\Exceptions\ControllerNotExistsException;
use Chevere\Components\Controller\ArgumentsWrap;
use Chevere\Components\Message\Message;
use Chevere\Components\App\Interfaces\AppInterface;
use Chevere\Components\App\Interfaces\ControllerRunnerInterface;
use Chevere\Components\App\Interfaces\MiddlewareRunnerInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;

/**
 * Application container ControllerInterface runner.
 */
final class ControllerRunner implements ControllerRunnerInterface
{
    private AppInterface $app;

    /** @var string */
    private string $controllerName;

    private MiddlewareRunnerInterface $middlewareRunner;

    /**
     * Sets the application on which the controllers will run.
     */
    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    public function run(string $controllerName): ControllerInterface
    {
        $this->controllerName = $controllerName;
        $this->assertControllerExists();
        $this->assertControllerName();
        if ($this->app->hasRouted() && $this->app->hasRequest()) {
            $this->handleRouteMiddleware();
        }
        $controller = new $controllerName($this->app);
        $controllerArguments = $this->getTypedArguments($controller);
        $controller(...$controllerArguments);

        return $controller;
    }

    private function getTypedArguments(ControllerInterface $controller): array
    {
        if ($this->app->hasArguments()) {
            $wrap = new ArgumentsWrap($controller, $this->app->arguments());

            return $wrap->typedArguments();
        }

        return [];
    }

    private function assertControllerExists(): void
    {
        if (!class_exists($this->controllerName)) {
            throw new ControllerNotExistsException(
                (new Message("Controller %controller% doesn't exists"))
                    ->code('%controller%', $this->controllerName)
                    ->toString()
            );
        }
    }

    private function assertControllerName(): void
    {
        if (!is_subclass_of($this->controllerName, ControllerInterface::class)) {
            throw new ControllerInterfaceException(
                (new Message('Controller %controller% must implement the %contract% interface'))
                    ->code('%controller%', $this->controllerName)
                    ->code('%contract%', ControllerInterface::class)
                    ->toString()
            );
        }
    }

    private function handleRouteMiddleware(): void
    {
        $route = $this->app->routed()->route();
        if (!$route->hasMiddlewareNameCollection()) {
            return;
        }
        $middlewareRunner = new MiddlewareRunner($route->middlewareNameCollection(), $this->app);
        $this->middlewareRunner = $middlewareRunner
            ->withRun();
    }
}
