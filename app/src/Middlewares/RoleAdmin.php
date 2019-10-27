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

namespace App\Middlewares;

use Chevere\Components\Http\Request\RequestException;
use Chevere\Contracts\App\MiddlewareHandlerContract;

class RoleAdmin implements MiddlewareHandlerContract
{
    public function handle(MiddlewareHandlerContract $handler): MiddlewareHandlerContract
    {
        $userRole = 'user';
        if ('admin' != $userRole) {
            throw new RequestException(401, sprintf('User must have the admin role, %s role found', $userRole));
        }
        return $handler;
    }
};
