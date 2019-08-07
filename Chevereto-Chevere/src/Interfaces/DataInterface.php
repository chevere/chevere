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

    public function setData(array $data): DataInterface;

    public function addData(array $data): DataInterface;

    public function appendData($var): DataInterface;

    public function getData(): ?array;

    public function toArray(): array;

    public function hasDataKey(string $key): bool;

    public function setDataKey(string $key, $var): DataInterface;

    public function getDataKey(string $key);

    public function removeDataKey(string $key): DataInterface;
}
