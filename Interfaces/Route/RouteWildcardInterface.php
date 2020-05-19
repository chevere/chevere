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

namespace Chevere\Interfaces\Route;

use Chevere\Interfaces\To\ToStringInterface;
use Chevere\Exceptions\Route\RouteWildcardInvalidRegexException;
use Chevere\Exceptions\Route\RouteWildcardNotFoundException;

interface RouteWildcardInterface
{
    /** Regex pattern used by default (no explicit where). */
    const REGEX_MATCH_DEFAULT = '[A-z0-9\\_\\-\\%]+';

    const ACCEPT_CHARS = '([a-z\_][\w_]*?)';
    const ACCEPT_CHARS_REGEX = '/^' . self::ACCEPT_CHARS . '+$/i';

    public function __construct(string $name);

    /**
     * Return an instance with the specified WildcardMatchInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified WildcardMatchInterface.
     *
     * @throws RouteWildcardInvalidRegexException if $match is an invalid regex match
     */
    public function withMatch(RouteWildcardMatchInterface $regexMatch): RouteWildcardInterface;

    /**
     * Provides access to the name.
     */
    public function name(): string;

    /**
     * Provides access to the braced name `{name}`
     */
    public function toString(): string;

    /**
     * Provides access to the WildcardMatchInterface instance.
     */
    public function match(): RouteWildcardMatchInterface;

    /**
     * Asserts that a given RoutePathInterface contains the wildcard.
     *
     * @param string $routePath A path including the wildcard, like `/{wildcard}`
     *
     * @throws RouteWildcardNotFoundException if the wildcard doesn't exists in the path
     */
    public function assertRoutePath(RoutePathInterface $routePath): void;
}
