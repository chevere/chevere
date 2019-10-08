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

    public function __construct(AppContract $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function runController(string $controller): ControllerContract
    {
        if (!is_subclass_of($controller, ControllerContract::class)) {
            throw new LogicException(
                (new Message('Controller %controller% must implement the %contract% interface'))
                    ->code('%controller%', $controller)
                    ->code('%contract%', ControllerContract::class)
                    ->toString()
            );
        }

        $this->handleRouteMiddleware();

        $controller = new $controller($this->app);

        if ($this->app->hasArguments()) {
            $wrap = new ArgumentsWrap($controller, $this->app->arguments());
            $controllerArguments = $wrap->typedArguments();
        } else {
            $controllerArguments = [];
        }

        $controller(...$controllerArguments);

        return $controller;
    }

    private function handleRouteMiddleware()
    {
        if ($this->app->route()) {
            $middlewares = $this->app->route()->middlewares();
            if (!empty($middlewares)) {
                $handler = new MiddlewareHandler($middlewares, $this->app);
                $handler->runner();
                if ($handler->exception) {
                    dd($handler->exception->getMessage(), 'Aborted at ' . __FILE__ . ':' . __LINE__);
                }
            }
        }
    }
}
