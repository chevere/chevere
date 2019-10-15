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

namespace Chevere\Contracts\Http;

use ArrayIterator;
use IteratorAggregate;

interface MethodsContract extends IteratorAggregate
{
    public function withAddedMethod(MethodContract $method): MethodsContract;

    public function has(string $method): bool;

    public function get(string $method): string;

    public function getIterator(): ArrayIterator;
}
