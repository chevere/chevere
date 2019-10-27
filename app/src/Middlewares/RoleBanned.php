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

use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\MiddlewareHandlerContract;

class RoleBanned implements MiddlewareHandlerContract
{
    public function __invoke(AppContract $app, MiddlewareHandlerContract $handler)
    {
        \dump(__FILE__);
        // $userRole = 'user';
        // if ('banned' == $userRole) {
        //     return $handler->stop($app);
        // }
        return $handler->process($app);
    }
};
