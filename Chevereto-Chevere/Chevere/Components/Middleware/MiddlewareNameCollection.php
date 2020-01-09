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

use Chevere\Components\Middleware\Contracts\MiddlewareNameCollectionContract;
use Chevere\Components\Middleware\Contracts\MiddlewareNameContract;

/**
 * A collection of MiddlewareContract names.
 */
final class MiddlewareNameCollection implements MiddlewareNameCollectionContract
{
    private array $array;

    private array $index;

    /**
     * Creates a new instance.
     */
    public function __construct(MiddlewareNameContract ...$middlewareNames)
    {
        $this->array = [];
        $this->index = [];
        foreach ($middlewareNames as $middlewareName) {
            $this->addMiddlewareName($middlewareName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMiddlewareName(MiddlewareNameContract $middlewareName): MiddlewareNameCollectionContract
    {
        $new = clone $this;
        $new->addMiddlewareName($middlewareName);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAny(): bool
    {
        return !empty($this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function has(MiddlewareNameContract $middlewareName): bool
    {
        return in_array($middlewareName->toString(), $this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }

    private function addMiddlewareName(MiddlewareNameContract $middlewareName): void
    {
        $name = $middlewareName->toString();
        $pos = array_search($name, $this->index);
        if (false !== $pos) {
            $this->array[$pos] = $middlewareName;
            $this->index[$pos] = $name;

            return;
        }

        $this->array[] = $middlewareName;
        $this->index[] = $name;
    }
}
