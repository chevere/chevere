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

use Chevere\Components\Middleware\Interfaces\MiddlewareNameCollectionInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;

/**
 * A collection of MiddlewareInterface names.
 */
final class MiddlewareNameCollection implements MiddlewareNameCollectionInterface
{
    private array $array;

    private array $index;

    /**
     * Creates a new instance.
     */
    public function __construct(MiddlewareNameInterface ...$middlewareNames)
    {
        $this->array = [];
        $this->index = [];
        foreach ($middlewareNames as $middlewareName) {
            $this->addMiddlewareName($middlewareName);
        }
    }

    public function withAddedMiddlewareName(MiddlewareNameInterface $middlewareName): MiddlewareNameCollectionInterface
    {
        $new = clone $this;
        $new->addMiddlewareName($middlewareName);

        return $new;
    }

    public function hasAny(): bool
    {
        return $this->index !== [];
    }

    public function has(MiddlewareNameInterface $middlewareName): bool
    {
        return in_array($middlewareName->toString(), $this->index);
    }

    public function toArray(): array
    {
        return $this->array;
    }

    private function addMiddlewareName(MiddlewareNameInterface $middlewareName): void
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
