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

namespace Chevere\TestApp\App\Middlewares;

use Chevere\Components\Middleware\Middleware;
use Chevere\Contracts\Http\RequestContract;

class TestMiddlewareVoid extends Middleware
{
    public function handle(RequestContract $request): void
    {
        // $userRole = 'user';
        // if ('banned' == $userRole) {
        //     throw new RequestException(401, 'User is banned');
        // }
    }
}
