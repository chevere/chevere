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

namespace Chevere\Components\Middleware;

use Chevere\Contracts\Middleware\MiddlewareNamesContract;

/**
 * A collection of MiddlewareContract names.
 */
final class MiddlewareNames implements MiddlewareNamesContract
{
    /** @var array */
    private $array;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->array = [];
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMiddlewareName(string $name): MiddlewareNamesContract
    {
        $middlewareName = new MiddlewareName($name);
        $new = clone $this;
        $new->array[] = $middlewareName->name();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAny(): bool
    {
        return !empty($this->array);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }
}
