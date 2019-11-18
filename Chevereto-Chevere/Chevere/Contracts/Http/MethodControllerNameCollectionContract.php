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

interface MethodControllerNameCollectionContract extends IteratorAggregate
{
    public function __construct(MethodControllerNameContract ...$methodControllerName);

    public function withAddedMethodControllerName(MethodControllerNameContract $methodController): MethodControllerNameCollectionContract;

    public function has(MethodContract $method): bool;

    public function get(MethodContract $method): MethodControllerNameContract;

    public function getIterator(): ArrayIterator;
}
