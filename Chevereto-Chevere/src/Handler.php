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

namespace Chevere;

use App\Middleware\RoleAdmin;
use Chevere\Contracts\App\AppContract;
use Chevere\Http\Response;
use Chevere\Interfaces\HandlerInterface;
use Chevere\Interfaces\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

/**
 * TODO: Add stop, redirect and other methods needed to alter the flow.
 */
/**
 * Handles middleware.
 */
final class Handler implements HandlerInterface
{
    /** @var AppContract */
    private $app;

    /** @var array */
    private $queue;

    /** @var bool */
    private $stopped;

    /**
     * @param array $queue an array containing callables or callable strings
     */
    // FIXME: Move this to another layer
    public function __construct(array $queue, AppContract $app)
    {
        $this->app = $app;
        $this->queue = $queue;
    }

    // FIXME: Move this to another layer
    public function runner()
    {
        reset($this->queue);

        return $this->handle();
    }

    public function handle(): MiddlewareInterface
    {
        $middleware = current($this->queue);
        if ($middleware) {
            next($this->queue);

            return new $middleware($this);
        }
    }

    public function stop(Throwable $e)
    {
        $this->stopped = true;
        $this->exception = $e;
    }
}
