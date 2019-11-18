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

namespace Chevere\Components\Http;

use ArrayIterator;
use Chevere\Components\Http\Exceptions\MethodNotFoundException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodControllerNameContract;
use Chevere\Contracts\Http\MethodControllerNameCollectionContract;

final class MethodControllerNameCollection implements MethodControllerNameCollectionContract
{
    /** @param array MethodControllerNameContract[] */
    private $array;

    /** @param array ['METHOD' => key,]*/
    private $index;

    /**
     * {@inheritdoc}
     */
    public function __construct(MethodControllerNameContract ...$methodControllerName)
    {
        $this->array = [];
        $this->index = [];
        foreach ($methodControllerName as $method) {
            $this->addMethodControllerName($method);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMethodControllerName(MethodControllerNameContract $methodControllerName): MethodControllerNameCollectionContract
    {
        $new = clone $this;
        $new->addMethodControllerName($methodControllerName);

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
    public function has(MethodContract $method): bool
    {
        return in_array($method->toString(), $this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function get(MethodContract $method): MethodControllerNameContract
    {
        $pos = array_search($method->toString(), $this->index);
        if (false === $pos) {
            throw new MethodNotFoundException(
                (new Message('Method %method% not found'))
                    ->code('%method%', $method->toString())
                    ->toString()
            );
        }

        return $this->array[$pos];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->array);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }

    private function addMethodControllerName(MethodControllerNameContract $methodControllerName): void
    {
        $name = $methodControllerName->method()->toString();
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
