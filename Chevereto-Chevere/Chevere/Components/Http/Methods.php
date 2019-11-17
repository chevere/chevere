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
use Chevere\Contracts\Http\MethodsContract;

final class Methods implements MethodsContract
{
    /** @param array [MethodContract,]*/
    private $methods;

    /** @param array ['METHOD' => key,]*/
    private $index;

    public function __construct(MethodContract ...$methods)
    {
        $new = clone $this;
        foreach ($methods as $method) {
            $new = $new
                ->withAddedMethod($method);
        }

        return $new;
    }

    public function withAddedMethod(MethodContract $method): MethodsContract
    {
        $new = clone $this;
        $new->methods[] = $method;
        $new->index[$method->name()] = array_key_last($new->methods);

        return $new;
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
