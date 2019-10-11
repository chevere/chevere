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

use Throwable;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\MiddlewareHandlerContract;
use Chevere\Interfaces\MiddlewareInterface;

/**
 * TODO: Add stop, redirect and other methods needed to alter the flow.
 */
final class MiddlewareHandler implements MiddlewareHandlerContract
{
    /** @var AppContract */
    private $app;

    /** @var array */
    private $queue;

    /** @var bool */
    private $stopped;

    /** @var Throwable */
    private $exception;

    /**
     * @param array $queue an array containing callables or callable strings
     */
    public function __construct(array $queue, AppContract $app)
    {
        $this->app = $app;
        $this->queue = $queue;
    }

    public function runner()
    {
        reset($this->queue);

        return $this->handle();
    }

    // FIXME: Returns null when condition = false
    public function handle(): MiddlewareInterface
    {
        $middleware = current($this->queue);
        if ($middleware) {
            next($this->queue);

            return new $middleware($this);
        }
    }

    public function stop(Throwable $throwable)
    {
        $this->stopped = true;
        $this->exception = $throwable;
    }
}
