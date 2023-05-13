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
