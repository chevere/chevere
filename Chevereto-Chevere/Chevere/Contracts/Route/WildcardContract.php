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

use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardStartWithNumberException;
use Chevere\Components\Route\Exceptions\WildcardInvalidCharsException;
use Chevere\Components\Route\Exceptions\WildcardInvalidRegexException;

interface WildcardContract
{
    /** Regex pattern used by default (no explicit where). */
    const REGEX_MATCH_DEFAULT = '[A-z0-9\_\-\%]+';

    const ACCEPT_CHARS = '([a-z\_][\w_]*?)';
    const ACCEPT_CHARS_REGEX = '/^' . self::ACCEPT_CHARS . '+$/i';
    // const ACCEPTED_CHARS_REGEX = '/^[a-z0-9_]+$/i';

    /**
     * Creates a new instance.
     *
     * @param string $name  The wildcard name
     * @param string $regex The regex patter, without delimeters
     *
     * @throws WildcardStartWithNumberException if $name starts with a number
     * @throws WildcardInvalidCharsException    if $name contains invalid chars
     * @throws WildcardInvalidRegexException    if $regex is invalid
     */
    public function __construct(string $name);

    /**
     * Return an instance with the specified regex.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified regex.
     *
     * @param string $regex a regular expresion, without delimiters
     *
     * @throws WildcardInvalidRegexException if $regex is invalid regex
     */
    public function withRegex(string $regex): WildcardContract;

    /**
     * Provides access to the name.
     */
    public function name(): string;

    /**
     * Provides access to the regex.
     */
    public function regex(): string;

    /**
     * Asserts that a given PathUriContract contains the wildcard.
     *
     * @param string $pathUri A path including the wildcard, like `/{wildcard}`
     *
     * @throws WildcardNotFoundException if the wildcard doesn't exists in the path
     */
    public function assertPathUri(PathUriContract $pathUri): void;
}
