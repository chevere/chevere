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

namespace Chevere\Http;

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Traits\VectorTrait;
use Chevere\DataStructure\Vector;
use Chevere\Http\Interfaces\MiddlewareNameInterface;
use Chevere\Http\Interfaces\MiddlewaresInterface;

final class Middlewares implements MiddlewaresInterface
{
    use VectorTrait;

    /**
     * @var VectorInterface<MiddlewareNameInterface>
     */
    private VectorInterface $vector;

    public function __construct(MiddlewareNameInterface ...$middleware)
    {
        $this->vector = new Vector(...$middleware);
    }

    public function withAppend(MiddlewareNameInterface ...$middleware): self
    {
        $new = clone $this;
        $new->vector = $new->vector->withPush(...$middleware);

        return $new;
    }

    public function withPrepend(MiddlewareNameInterface ...$middleware): self
    {
        $new = clone $this;
        $new->vector = $new->vector->withUnshift(...$middleware);

        return $new;
    }
}
