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

namespace Chevere\Components\Http;

use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Message\Message;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameCollectionInterface;

final class MethodControllerNameCollection implements MethodControllerNameCollectionInterface
{
    /** @param array MethodControllerNameInterface[] */
    private array $array;

    /** @param array ['METHOD' => key,]*/
    private array $index;

    /**
     * Creates a new instance.
     */
    public function __construct(MethodControllerNameInterface ...$methodControllerName)
    {
        $this->array = [];
        $this->index = [];
        foreach ($methodControllerName as $method) {
            $this->addMethodControllerName($method);
        }
    }

    public function withAddedMethodControllerName(MethodControllerNameInterface $methodControllerName): MethodControllerNameCollectionInterface
    {
        $new = clone $this;
        $new->addMethodControllerName($methodControllerName);

        return $new;
    }

    public function hasAny(): bool
    {
        return $this->index !== [];
    }

    public function has(MethodInterface $method): bool
    {
        return in_array($method::name(), $this->index);
    }

    public function get(MethodInterface $method): MethodControllerNameInterface
    {
        $pos = array_search($method::name(), $this->index);
        if (false === $pos) {
            throw new MethodNotFoundException(
                (new Message('Method %method% not found'))
                    ->code('%method%', $method::name())
                    ->toString()
            );
        }

        return $this->array[$pos];
    }

    public function toArray(): array
    {
        return $this->array;
    }

    private function addMethodControllerName(MethodControllerNameInterface $methodControllerName): void
    {
        $name = $methodControllerName->method()::name();
        $pos = array_search($name, $this->index);
        if (false !== $pos) {
            $this->array[$pos] = $methodControllerName;
            $this->index[$pos] = $name;

            return;
        }

        $this->array[] = $methodControllerName;
        $this->index[] = $name;
    }
}
