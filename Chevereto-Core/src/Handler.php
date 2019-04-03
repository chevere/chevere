<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Chevereto\Core\Interfaces\HandlerInterface;

/**
 * TODO: Add stop, redirect and other methods needed to alter the flow.
 */
/**
 * Handles middleware.
 */
class Handler implements HandlerInterface
{
    use Traits\CallableTrait;

    protected $queue;

    /**
     * Creates the Handler queue.
     *
     * @param array $queue an array containing callables or callable strings
     */
    public function __construct(array $queue = null)
    {
        if (null != $queue) {
            $this->setQueue($queue);
        }
    }

    /**
     * Set Handler queue.
     */
    public function setQueue(array $queue): self
    {
        foreach ($queue as $k => &$v) {
            $v = $this->getCallable($v);
        }
        $this->queue = $queue;

        return $this;
    }

    /**
     * Initiates the Handler runner.
     */
    public function runner(App $app)
    {
        reset($this->queue);

        return $this->process($app);
    }

    /**
     * Process calling.
     */
    public function process(App $app)
    {
        $middleware = current($this->queue);
        if ($middleware) {
            next($this->queue);

            return $middleware($app, $this);
        }
    }

    public function stop($app)
    {
        $app->terminate(__METHOD__.' Terminated the app execution.');
    }
}
class HandlerException extends CoreException
{
}
