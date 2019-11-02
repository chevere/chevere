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

use Chevere\Components\App\Exceptions\AppWithoutRequestException;
use Chevere\Components\App\Exceptions\MiddlewareContractException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\MiddlewareRunnerContract;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Middleware\MiddlewareContract;

final class MiddlewareRunner implements MiddlewareRunnerContract
{
    /** @var AppContract */
    private $app;

    /** @var array */
    private $queue;

    /** @var bool */
    private $ran;

    /**
     * @param array $queue an array containing callable Middlewares
     * @param AppContract $app The application container
     */
    public function __construct(array $queue, AppContract $app)
    {
        $this->app = $app;
        $this->assertAppWithRequest();
        $this->queue = $queue;
    }

    public function withRun(): MiddlewareRunnerContract
    {
        $new = clone $this;
        $new->run();
        $new->ran = true;

        return $new;
    }

    private function run(): void
    {
        foreach ($this->queue as $middleware) {
            if (!is_subclass_of($middleware, MiddlewareContract::class)) {
                throw new MiddlewareContractException(
                    (new Message('Middleware %middleware% must implement the %contract% contract'))
                        ->code('%middleware%', $middleware)
                        ->code('%contract%', MiddlewareContract::class)
                        ->toString()
                );
            }
            (new $middleware())
                ->handle(
                    $this->app->request()
                );
        }
    }

    private function assertAppWithRequest(): void
    {
        if (!$this->app->hasRequest()) {
            throw new AppWithoutRequestException(
                (new Message('Instance of %type% must contain a %contract% contract'))
                    ->code('%type%', AppContract::class)
                    ->code('%contract%', RequestContract::class)
                    ->toString()
            );
        }
    }
}
