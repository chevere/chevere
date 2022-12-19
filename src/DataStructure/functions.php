<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\DataStructure;

use Chevere\DataStructure\Interfaces\MapInterface;
use Chevere\DataStructure\Interfaces\VectorInterface;

/**
 * Creates an ordered data map (array) from named arguments.
 *
 * @return array<string, mixed>
 */
function data(mixed ...$argument): array
{
    /** @var array<string, mixed> */
    return $argument;
}

/**
 * Convert MapInterface to array.
 *
 * @param MapInterface<mixed> $map
 * @return array<string, mixed>
 */
function mapToArray(MapInterface $map): array
{
    return iterator_to_array($map->getIterator());
}

/**
 * Convert VectorInterface to array.
 *
 * @param VectorInterface<mixed> $vector
 * @return array<mixed> $vector
 */
function vectorToArray(VectorInterface $vector): array
{
    return iterator_to_array($vector->getIterator());
}
