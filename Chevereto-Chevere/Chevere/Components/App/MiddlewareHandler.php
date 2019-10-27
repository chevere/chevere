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
use Chevere\Contracts\App\MiddlewareHandlerContract;

/**
 * TODO: Add redirect and other methods needed to alter the flow.
 */
final class MiddlewareHandler implements MiddlewareHandlerContract
{
    /** @var AppContract */
    private $app;

    /** @var array */
    private $queue;

    /** @var bool */
    private $isStopped;

    /** @var RequestException */
    private $exception;

    /**
     * @param array $queue an array containing callable Middlewares
     * @param AppContract $app The application container
     */
    public function __construct(array $queue, AppContract $app)
    {
        $this->app = $app;
        $this->queue = $queue;
    }

    public function runner(): MiddlewareHandlerContract
    {
        reset($this->queue);

        return $this->handle();
    }

    /**
     * Stops the middleware execution chain.
     * 
     * @param RequestException $exception An exception describing what went wrong
     */
    public function stop(RequestException $exception): void
    {
        $this->isStopped = true;
        $this->exception = $exception;
    }

    public function isStopped(): bool
    {
        return $this->isStopped;
    }

    public function exception(): RequestException
    {
        return $this->exception;
    }

    private function handle(): MiddlewareHandlerContract
    {
        $middleware = current($this->queue);
        if ($middleware) {
            next($this->queue);

            return (new $middleware())
                ->handle($this);
        }

        return $this;
    }
}
