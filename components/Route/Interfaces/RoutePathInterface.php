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

interface RoutePathInterface extends ToStringInterface
{
    const REGEX_DELIMITER_CHAR = '/';

    /** Regex pattern used to catch {wildcard} */
    const REGEX_WILDCARD_SEARCH = self::REGEX_DELIMITER_CHAR . '{' . RouteWildcardInterface::ACCEPT_CHARS . '}' . self::REGEX_DELIMITER_CHAR . 'i';

    public function __construct(string $path);

    /**
     * @return string Uri path.
     */
    public function toString(): string;

    /**
     * Provides access to the key string, which is a representation of the path
     * with placeholders converting `/api/articles/{wildcard}` to `/api/articles/{0}`
     */
    public function key(): string;

    /**
     * Provides access to the path uri regex pattern.
     */
    public function regex(): string;

    /**
     * Returns a boolean indicating whether the instance has a RouteWildcardsInterface.
     */
    public function hasRouteWildcards(): bool;

    /**
     * Provides access to the RouteWildcardsInterface instance.
     */
    public function routeWildcards(): RouteWildcardsInterface;

    /**
     * Return an instance with the specified WildcardInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified WildcardInterface.
     */
    public function withWildcard(RouteWildcardInterface $wildcard): RoutePathInterface;

    /**
     * Provides an array matching wildcards for the given request uri.
     *
     * @return array [wildcardName => matchedValue,]
     */
    public function matchFor(string $requestUri): array;

    /**
     * Provide a request uri for the given explicit wildcards.
     */
    public function uriFor(array $wildcards): string;
}
