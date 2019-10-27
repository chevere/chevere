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

use Chevere\Components\Http\Request\RequestException;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\MiddlewareRunnerContract;

final class MiddlewareRunner implements MiddlewareRunnerContract
{
    /** @var AppContract */
    private $app;

    /** @var array */
    private $queue;

    /**
     * @param array $queue an array containing callable Middlewares
     * @param AppContract $app The application container
     */
    public function __construct(array $queue, AppContract $app)
    {
        $this->app = $app;
        $this->queue = $queue;
        $this->handle();
    }

    private function handle(): MiddlewareRunnerContract
    {
        $middleware = current($this->queue);
        if ($middleware) {
            next($this->queue);

            return (new $middleware())
                ->handle($this);
        }
        dd($this);
    }
}
