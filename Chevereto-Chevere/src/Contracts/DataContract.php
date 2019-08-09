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

namespace Chevere\Contracts;

use ArrayIterator;
use Countable;
use IteratorAggregate;

interface DataContract extends ToArrayContract, IteratorAggregate, Countable
{
    public function __construct();

    public function getIterator(): ArrayIterator;

    public function count(): int;

    public function set(array $data): DataContract;

    public function add(array $data): DataContract;

    public function append($var): DataContract;

    public function get(): ?array;

    public function toArray(): array;

    public function hasKey(string $key): bool;

    public function setKey(string $key, $var): DataContract;

    public function getKey(string $key);

    public function removeKey(string $key): DataContract;
}
