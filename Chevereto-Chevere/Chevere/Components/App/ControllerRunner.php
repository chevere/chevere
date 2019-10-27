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

use LogicException;

use Chevere\Components\Controller\ArgumentsWrap;
use Chevere\Components\Http\Request\RequestException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\ControllerRunnerContract;
use Chevere\Contracts\Controller\ControllerContract;

/**
 * Application container ControllerContract runner.
 */
final class ControllerRunner implements ControllerRunnerContract
{
    /** @var AppContract */
    private $app;

    /** @var string */
    private $controllerName;

    /**
     * {@inheritdoc}
     */
    public function __construct(AppContract $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function run(string $controllerName): ControllerContract
    {
        $this->controllerName = $controllerName;
        $this->assertControllerName();
        $this->handleRouteMiddleware();
        $controller = new $controllerName($this->app);
        $controllerArguments = $this->getTypedArguments($controller);
        $controller(...$controllerArguments);

        return $controller;
    }

    private function getTypedArguments(ControllerContract $controller): array
    {
        if ($this->app->hasArguments()) {
            $wrap = new ArgumentsWrap($controller, $this->app->arguments());
            return $wrap->typedArguments();
        }
        return [];
    }

    private function assertControllerName(): void
    {
        if (!is_subclass_of($this->controllerName, ControllerContract::class)) {
            throw new LogicException(
                (new Message('Controller %controller% must implement the %contract% interface'))
                    ->code('%controller%', $this->controllerName)
                    ->code('%contract%', ControllerContract::class)
                    ->toString()
            );
        }
    }

    private function handleRouteMiddleware(): void
    {
        if ($this->app->route()) {
            $middlewares = $this->app->route()->middlewares();
            if (!empty($middlewares)) {
                $handler = new MiddlewareHandler($middlewares, $this->app);
                $handler->runner();
            }
        }
    }
}
