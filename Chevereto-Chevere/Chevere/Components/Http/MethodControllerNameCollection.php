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
        $new = clone $this;
        foreach ($methodControllerName as $method) {
            $new = $new
                ->withAddedMethodControllerName($method);
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMethodControllerName(MethodControllerNameContract $methodControllerName): MethodControllerNameCollectionContract
    {
        $new = clone $this;
        $new->array[] = $methodControllerName;
        $new->index[$methodControllerName->method()->toString()] = array_key_last($new->array);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function has(MethodContract $method): bool
    {
        return isset($this->index[$method->toString()]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(MethodContract $method): MethodControllerNameContract
    {
        return $this->array[$this->index[$method->toString()]];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->array);
    }
}
