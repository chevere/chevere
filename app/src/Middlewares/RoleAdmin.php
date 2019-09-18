<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Middlewares;

use Chevere\Contracts\App\MiddlewareHandlerContract;
use Chevere\Interfaces\MiddlewareInterface;
use Chevere\Http\Request\RequestException;

class RoleAdmin implements MiddlewareInterface
{
    public function __construct(MiddlewareHandlerContract $handler)
    {
        $userRole = 'user';
        if ('admin' != $userRole) {
            return $handler->stop(
                new RequestException(401, sprintf('User must have the admin role, %s role found', $userRole))
            );
        }
        return $handler->handle();
    }
};
