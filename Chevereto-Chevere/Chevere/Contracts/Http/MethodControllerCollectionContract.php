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

interface MethodControllerCollectionContract extends IteratorAggregate
{
    public function __construct(MethodControllerContract ...$methodController);

    public function withAddedMethodController(MethodControllerContract $methodController): MethodControllerCollectionContract;

    public function has(MethodContract $method): bool;

    public function get(MethodContract $method): MethodControllerContract;

    public function getIterator(): ArrayIterator;
}
