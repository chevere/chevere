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
use Chevere\Components\Http\Interfaces\MethodControllerNameCollectionInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Message\Message;

final class MethodControllerNameCollection implements MethodControllerNameCollectionInterface
{
    private MethodControllerNameObjects $objects;

    /** @param array ['METHOD' => key,]*/
    private array $index = [];

    private int $count = -1;

    public function __construct(MethodControllerNameInterface ...$methodControllerName)
    {
        $this->objects = new MethodControllerNameObjects();
        foreach ($methodControllerName as $method) {
            $this->storeMethodControllerName($method);
        }
    }

    public function count(): int
    {
        return $this->count + 1;
    }

    public function withAddedMethodControllerName(MethodControllerNameInterface $methodControllerName): MethodControllerNameCollectionInterface
    {
        $new = clone $this;
        $new->storeMethodControllerName($methodControllerName);

        return $new;
    }

    public function hasAny(): bool
    {
        return $this->count > -1;
    }

    public function hasMethod(MethodInterface $method): bool
    {
        return in_array($method::name(), $this->index);
    }

    public function getMethod(MethodInterface $method): MethodControllerNameInterface
    {
        $pos = array_search($method::name(), $this->index);
        if (false === $pos) {
            throw new MethodNotFoundException(
                (new Message('Method %method% not found'))
                    ->code('%method%', $method::name())
                    ->toString()
            );
        }
        $this->objects->rewind();
        for ($i = 0; $i < $pos; $i++) {
            $this->objects->next();
        }

        return $this->objects->current();
    }

    public function objects(): MethodControllerNameObjects
    {
        return $this->objects;
    }

    private function storeMethodControllerName(MethodControllerNameInterface $methodControllerName): void
    {
        $name = $methodControllerName->method()::name();
        $pos = array_search($name, $this->index);
        if (false !== $pos) {
            $this->objects->append(
                $methodControllerName,
                $pos
            );
            $this->index[$pos] = $name;

            return;
        }
        $this->count++;
        $this->objects->append(
            $methodControllerName,
            $this->count
        );
        $this->index[$this->count] = $name;
    }
}
