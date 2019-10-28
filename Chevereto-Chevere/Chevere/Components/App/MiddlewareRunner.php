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

    /** @var bool */
    private $ran;

    /**
     * @param array $queue an array containing callable Middlewares
     * @param AppContract $app The application container
     */
    public function __construct(array $queue, AppContract $app)
    {
        $this->app = $app;
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
            (new $middleware())
                ->handle(
                    $this->app->request()
                );
        }
    }
}
