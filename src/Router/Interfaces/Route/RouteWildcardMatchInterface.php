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

namespace Chevere\Router\Interfaces\Route;

use Chevere\Throwable\Exceptions\UnexpectedValueException;
use Stringable;

/**
 * Describes the component in charge of defining a route wildcard match.
 */
interface RouteWildcardMatchInterface extends Stringable
{
    /**
     * @param string $string A regular expression match statement.
     * @throws UnexpectedValueException If `$string` is an invalid regex matcher.
     */
    public function __construct(string $string);

    /**
     * Same as `toString` but with starting `^` and ending `$`.
     */
    public function toAnchored(): string;
}
