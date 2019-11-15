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
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\Exceptions\WildcardStartWithNumberException;
use Chevere\Components\Route\Exceptions\WildcardInvalidCharsException;
use Chevere\Components\Route\Exceptions\WildcardInvalidRegexException;

interface WildcardContract
{
    const ACCEPTED_CHARS_REGEX = '/^[a-z0-9_]+$/i';

    /**
     * Creates a new instance.
     *
     * @param string $wildcardName The wildcard name
     * @param string $regex        The regex patter, without delimeters
     *
     * @throws WildcardStartWithNumberException if $wildcardName starts with a number
     * @throws WildcardInvalidCharsException    if $wildcardName contains invalid chars
     * @throws WildcardInvalidRegexException    if $regex is invalid
     */
    public function __construct(string $wildcardName, string $regex);

    /**
     * Asserts that $path contains $wildcardName.
     *
     * @param string A path including the wildcard, like `/{wildcard}`
     *
     * @throws WildcardNotFoundException if the wildcard doesn't exists in the path
     */
    public function assertPath(PathUri $pathUri): void;
}
