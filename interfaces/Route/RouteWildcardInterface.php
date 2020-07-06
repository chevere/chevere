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

use Chevere\Exceptions\Route\RouteWildcardInvalidRegexException;
use Chevere\Exceptions\Route\RouteWildcardNotFoundException;
use Chevere\Interfaces\To\ToStringInterface;

interface RouteWildcardInterface
{
    /** Regex pattern used by default (no explicit where). */
    const REGEX_MATCH_DEFAULT = '[A-z0-9\\_\\-\\%]+';

    const ACCEPT_CHARS = '([a-z\_][\w_]*?)';
    const ACCEPT_CHARS_REGEX = '/^' . self::ACCEPT_CHARS . '+$/i';

    public function __construct(string $name, RouteWildcardMatchInterface $match);

    /**
     * Provides access to the name.
     */
    public function name(): string;

    /**
     * Provides access to the WildcardMatchInterface instance.
     */
    public function match(): RouteWildcardMatchInterface;
}
