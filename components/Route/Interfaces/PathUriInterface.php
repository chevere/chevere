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

namespace Chevere\Components\Route\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface PathUriInterface extends ToStringInterface
{
    /** string Regex pattern used to catch {wildcard} */
    const REGEX_WILDCARD_SEARCH = '/{' . WildcardInterface::ACCEPT_CHARS . '}/i';

    public function __construct(string $path);

    /**
     * @return string Uri path.
     */
    public function toString(): string;

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
