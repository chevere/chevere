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

namespace Chevere\TestApp\App\Middlewares;

use Chevere\Components\Middleware\Middleware;
use Chevere\Components\Http\Interfaces\RequestInterface;

class TestMiddlewareVoid extends Middleware
{
    public function handle(RequestInterface $request): void
    {
        // $userRole = 'user';
        // if ('banned' == $userRole) {
        //     throw new RequestException(401, 'User is banned');
        // }
    }
}
