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
use Chevere\Components\Common\Contracts\ToStringContract;

interface MethodContract extends ToStringContract
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

    public function __construct(string $name);

    /**
     * @return string Method name.
     */
    public function toString(): string;
}
