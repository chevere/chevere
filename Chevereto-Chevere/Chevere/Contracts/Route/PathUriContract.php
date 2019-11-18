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

use Chevere\Components\Route\Exceptions\PathUriForwardSlashException;
use Chevere\Components\Route\Exceptions\PathUriInvalidCharsException;
use Chevere\Components\Route\Exceptions\PathUriUnmatchedBracesException;
use Chevere\Components\Route\Exceptions\PathUriUnmatchedWildcardsException;
use Chevere\Components\Route\Exceptions\WildcardReservedException;
use Chevere\Components\Route\Exceptions\WildcardRepeatException;

interface PathUriContract
{
    /** string Regex pattern used to catch {wildcard} */
    const REGEX_WILDCARD_SEARCH = '/{' . WildcardContract::ACCEPT_CHARS . '}/i';

    /**
     * Creates a new instance.
     *
     * @param string $path a path uri like `/path/{wildcard}`
     *
     * @throws PathUriForwardSlashException       if $path doesn't start with forward slash
     * @throws PathUriInvalidCharsException       if $path contains invalid chars
     * @throws PathUriUnmatchedBracesException    if $path contains unmatched braces (must be paired)
     * @throws PathUriUnmatchedWildcardsException if $path contains wildcards that don't match the number of braces
     * @throws WildcardReservedException          if $path contains reserved wildcards
     * @throws WildcardRepeatException            if $path contains repeated wildcards
     */
    public function __construct(string $path);

    /**
     * Provides access to the path.
     */
    public function path(): string;

    /**
     * Provides access to the key string.
     */
    public function key(): string;

    /**
     * Returns a boolean indicating whether the instance has handlebars `{}`.
     */
    public function hasWildcards(): bool;

    /**
     * Provides acess to the wildcards array.
     */
    public function wildcards(): array;
}
