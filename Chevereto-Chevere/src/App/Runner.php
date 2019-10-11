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
use Chevere\Message\Message;
use Chevere\Controller\ArgumentsWrap;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\RunnerContract;
use Chevere\Contracts\Controller\ControllerContract;

final class Runner implements RunnerContract
{
    /** @var AppContract */
    private $app;

    /** @var string */
    private $controllerName;

    public function __construct(AppContract $app)
    {
        $this->app = $app;
    }

    public function withControllerName(string $controllerName): RunnerContract
    {
        $new = clone $this;
        $new->controllerName = $controllerName;
        $new->assertControllerName();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function run(): ControllerContract
    {
        if (!isset($this->controllerName)) {
            throw new LogicException(
                (new Message("Instance of class %class% lacks of a controller"))
                    ->code('%class%', __CLASS__)
            );
        }
        $this->handleRouteMiddleware();
        $controller = new $this->controllerName($this->app);
        $controllerArguments = $this->getControllerArguments($controller);
        $controller(...$controllerArguments);

        return $controller;
    }

    private function getControllerArguments($controller): array
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

    private function handleRouteMiddleware()
    {
        if ($this->app->route()) {
            $middlewares = $this->app->route()->middlewares();
            if (!empty($middlewares)) {
                $handler = new MiddlewareHandler($middlewares, $this->app);
                $handler->runner();
                // if ($handler->exception) {
                //     dd($handler->exception->getMessage(), 'Aborted at ' . __FILE__ . ':' . __LINE__);
                // }
            }
        }
    }
}
