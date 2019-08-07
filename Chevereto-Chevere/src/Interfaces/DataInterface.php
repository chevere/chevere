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

namespace Chevere\Interfaces;

use ArrayIterator;
use Countable;
use IteratorAggregate;

interface DataInterface extends ToArrayInterface, IteratorAggregate, Countable
{
    public function __construct(array $data = null);

    public function getIterator(): ArrayIterator;

    public function count(): int;

    public function set(array $data): DataInterface;

    public function add(array $data): DataInterface;

    public function append($var): DataInterface;

    public function get(): ?array;

    public function toArray(): array;

    public function hasKey(string $key): bool;

    public function setKey(string $key, $var): DataInterface;

    public function getKey(string $key);

    public function removeKey(string $key): DataInterface;
}
