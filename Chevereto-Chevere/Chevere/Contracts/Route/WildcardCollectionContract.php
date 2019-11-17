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

namespace Chevere\Contracts\Route;

use ArrayIterator;
use IteratorAggregate;

interface WildcardCollectionContract extends IteratorAggregate
{
    /**
     * Creates a new instance.
     */
    public function __construct(WildcardContract ...$wildcard);

    public function withAddedWildcard(WildcardContract $wildcard): WildcardCollectionContract;

    public function has(WildcardContract $wildcard): bool;

    public function get(WildcardContract $wildcard): WildcardContract;

    public function hasPos(int $pos): bool;

    public function getPos(int $pos): WildcardContract;

    public function getIterator(): ArrayIterator;
}
