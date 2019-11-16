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

interface PathUriContract
{
    /**
     * Creates a new instance.
     *
     * @param string $path a path uri like `/path/{wildcard}`
     *
     * @throws PathUriInvalidFormatException   if $path format is invalid or if it doesn't start with forward slash
     * @throws PathUriUnmatchedBracesException if $path contains unmatched braces
     * @throws WildcardReservedException       if $path contains reserved wildcards
     */
    public function __construct(string $path);

    /**
     * Provides access to the path.
     */
    public function path(): string;

    /**
     * Returns a boolean indicating whether the instance has handlebars `{}`.
     */
    public function hasWildcards(): bool;

    /**
     * Returns an array containing the wildcard match (if any).
     *
     * @return array 0 => [{wildcard1}, {wildcard2},...], 1 => [wildcard1, wildcard2,...]
     */
    public function wildcardsMatch(): array;
}
