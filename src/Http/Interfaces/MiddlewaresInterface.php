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

namespace Chevere\Http\Interfaces;

use Chevere\DataStructure\Interfaces\IntegerKeysInterface;
use Countable;
use Iterator;
use IteratorAggregate;

/**
 * Describes the component in charge of collecting HTTP server middleware.
 * @extends IteratorAggregate<int, MiddlewareNameInterface>
 */
interface MiddlewaresInterface extends Countable, IntegerKeysInterface, IteratorAggregate
{
    /**
     * Return an instance with the specified appended `$middleware`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified appended `$middleware`.
     *
     * This middleware will be added to the end of the middleware stack.
     */
    public function withAppend(MiddlewareNameInterface ...$middleware): self;

    /**
     * Return an instance with the specified prepend `$middleware`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified prepend `$middleware`.
     *
     * This middleware will be added to the beginning of the stack.
     */
    public function withPrepend(MiddlewareNameInterface ...$middleware): self;

    /**
     * @return Iterator<int, MiddlewareNameInterface>
     */
    public function getIterator(): Iterator;
}
