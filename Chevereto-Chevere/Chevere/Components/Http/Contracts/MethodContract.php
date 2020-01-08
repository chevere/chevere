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

namespace Chevere\Components\Http\Contracts;

use InvalidArgumentException;

interface MethodContract
{
    /** Array containing all the known HTTP methods. */
    const ACCEPT_METHOD_NAMES = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'COPY',
        'HEAD',
        'OPTIONS',
        'LINK',
        'UNLINK',
        'PURGE',
        'LOCK',
        'UNLOCK',
        'PROPFIND',
        'VIEW',
        'TRACE',
        'CONNECT'
    ];

    /**
     * Creates a new instance.
     *
     * @throws InvalidArgumentException if the $name isn't included in ACCEPT_METHOD_NAMES
     */
    public function __construct(string $name);

    /**
     * Returns the method name.
     */
    public function toString(): string;
}
