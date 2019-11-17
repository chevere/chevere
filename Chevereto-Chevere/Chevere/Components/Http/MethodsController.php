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
use Chevere\Contracts\Http\MethodControllerContract;
use Chevere\Contracts\Http\MethodControllerCollectionContract;

final class MethodControllerCollection implements MethodControllerCollectionContract
{
    /** @param array [MethodControllerContract,]*/
    private $array;

    /** @param array ['METHOD' => key,]*/
    private $index;

    /**
     * {@inheritdoc}
     */
    public function __construct(MethodControllerContract ...$methodController)
    {
        $new = clone $this;
        foreach ($methodController as $method) {
            $new = $new
                ->withAddedMethodController($method);
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedMethodController(MethodControllerContract $method): MethodControllerCollection
    {
        $new = clone $this;
        $new->array[] = $method;
        $new->index[$method->name()] = array_key_last($new->array);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $method): bool
    {
        return isset($this->index[$method]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $method): string
    {
        return $this->array[$this->index[$method]];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->array);
    }
}
