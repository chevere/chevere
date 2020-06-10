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

namespace Chevere\Components\Middleware;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Middleware\MiddlewaresInterface;
use Ds\Map;
use Ds\Set;
use Psr\Http\Server\MiddlewareInterface;

/**
 * A collection of MiddlewareInterface names.
 */
final class Middlewares implements MiddlewaresInterface
{
    use DsMapTrait;

    /** @var Map [middlewareClassName => pos] */
    private Map $map;

    /** @var Set [MiddlewareInterface,] */
    private Set $set;

    public function __construct()
    {
        $this->map = new Map;
        $this->set = new Set;
    }

    public function withAddedMiddleware(MiddlewareInterface $middleware): MiddlewaresInterface
    {
        if ($this->set->contains($middleware)) {
            throw new OverflowException;
        }
        $new = clone $this;
        $className = get_class($middleware);
        $new->set->add($middleware);
        $new->map->put($className, count($new->set) - 1);

        return $new;
    }

    public function has(MiddlewareInterface $middleware): bool
    {
        return $this->set->contains($middleware);
    }
}
