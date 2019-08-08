<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\HttpFoundation;

use ArrayIterator;
use IteratorAggregate;
use Chevere\Contracts\HttpFoundation\MethodContract;
use Chevere\Contracts\HttpFoundation\MethodsContract;

/**
 * Api provides a static method to read the exposed API inside the app runtime.
 */
final class Methods implements MethodsContract, IteratorAggregate
{
    /** @param array [MethodContract,]*/
    private $methods;

    /** @param array ['METHOD' => key,]*/
    private $index;

    public function add(MethodContract $method): void
    {
        $this->methods[] = $method;
        $this->index[$method->method()] = array_key_last($this->methods);
    }

    public function has(string $method): bool
    {
        return isset($this->index[$method]);
    }

    public function get(string $method): string
    {
        return $this->methods[$this->index[$method]];
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->methods);
    }
}
