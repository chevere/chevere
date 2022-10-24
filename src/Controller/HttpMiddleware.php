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

namespace Chevere\Controller;

use Chevere\Controller\Interfaces\HttpMiddlewareInterface;
use Chevere\DataStructure\Traits\VectorTrait;
use Ds\Vector;
use Psr\Http\Server\MiddlewareInterface;

final class HttpMiddleware implements HttpMiddlewareInterface
{
    use VectorTrait;

    /**
     * @var Vector<MiddlewareInterface>
     */
    private Vector $vector;

    public function __construct(MiddlewareInterface ...$middleware)
    {
        $this->vector = new Vector();
        $this->vector->push(...$middleware);
    }

    public function withAppend(MiddlewareInterface ...$middleware): self
    {
        $new = clone $this;
        $new->vector->push(...$middleware);

        return $new;
    }

    public function withPrepend(MiddlewareInterface ...$middleware): self
    {
        $new = clone $this;
        $new->vector->unshift(...$middleware);

        return $new;
    }
}
