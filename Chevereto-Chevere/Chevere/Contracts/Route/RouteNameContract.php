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

use Chevere\Components\Route\Exceptions\RouteInvalidNameException;

interface RouteNameContract
{
    /** Regex pattern used to validate route name. */
    const REGEX = '/^[\w\-\.]+$/i';

    /**
     * Creates a new instance.
     *
     * @throws RouteInvalidNameException if $name doesn't match REGEX
     */
    public function __construct(string $name);

    /**
     * Provides access to the route name string.
     */
    public function toString(): string;
}
