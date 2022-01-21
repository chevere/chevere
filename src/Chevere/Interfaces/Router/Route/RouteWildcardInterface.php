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

namespace Chevere\Interfaces\Router\Route;

use Chevere\Exceptions\Router\Route\RouteWildcardInvalidException;
use Stringable;

/**
 * Describes the component in charge of defining a route wildcard.
 */
interface RouteWildcardInterface extends Stringable
{
    public const ACCEPT_CHARS = '([a-z\_][\w_]*?)';

    public const ACCEPT_CHARS_REGEX = '/^' . self::ACCEPT_CHARS . '+$/i';

    /**
     * @throws RouteWildcardInvalidException
     */
    public function __construct(string $name, RouteWildcardMatchInterface $match);

    /**
     * Provides access to the match instance.
     */
    public function match(): RouteWildcardMatchInterface;
}
