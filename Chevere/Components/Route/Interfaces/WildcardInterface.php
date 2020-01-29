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

use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardInvalidRegexException;

interface WildcardInterface
{
    /** Regex pattern used by default (no explicit where). */
    const REGEX_MATCH_DEFAULT = '[A-z0-9\\_\\-\\%]+';

    const ACCEPT_CHARS = '([a-z\_][\w_]*?)';
    const ACCEPT_CHARS_REGEX = '/^' . self::ACCEPT_CHARS . '+$/i';

    public function __construct(string $name);

    /**
     * Return an instance with the specified RegexMatchInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RegexMatchInterface.
     *
     * @throws WildcardInvalidRegexException if $match is an invalid regex match
     */
    public function withMatch(WildcardMatchInterface $regexMatch): WildcardInterface;

    /**
     * Provides access to the name.
     */
    public function name(): string;

    /**
     * Provides access to the WildcardMatchInterface instance.
     */
    public function match(): WildcardMatchInterface;

    /**
     * Asserts that a given PathUriInterface contains the wildcard.
     *
     * @param string $pathUri A path including the wildcard, like `/{wildcard}`
     *
     * @throws WildcardNotFoundException if the wildcard doesn't exists in the path
     */
    public function assertPathUri(PathUriInterface $pathUri): void;
}
