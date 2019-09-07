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

namespace Chevere\Http;

use ArrayIterator;
use Chevere\Contracts\Http\MethodContract;
use Chevere\Contracts\Http\MethodsContract;

final class Methods implements MethodsContract
{
    /** @param array [MethodContract,]*/
    private $methods;

    /** @param array ['METHOD' => key,]*/
    private $index;

    public function __construct(MethodContract ...$methods)
    {
        foreach ($methods as $k => $method) {
            $this->add($method);
        }
    }

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
