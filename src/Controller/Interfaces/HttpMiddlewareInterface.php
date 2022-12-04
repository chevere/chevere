<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Controller\Interfaces;

use Chevere\DataStructure\Interfaces\VectorInterface;
use Iterator;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Describes the component in charge of collecting middleware.
 */
interface HttpMiddlewareInterface extends VectorInterface
{
    /**
     * Return an instance with the specified appended `$middleware`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified appended `$middleware`.
     *
     * This middleware will be added to the end of the middleware stack.
     */
    public function withAppend(MiddlewareInterface ...$middleware): self;

    /**
     * Return an instance with the specified prepend `$middleware`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified prepend `$middleware`.
     *
     * This middleware will be added to the beginning of the stack.
     */
    public function withPrepend(MiddlewareInterface ...$middleware): self;

    /**
     * @return Iterator<int, MiddlewareInterface>
     */
    public function getIterator(): Iterator;
}