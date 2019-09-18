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

use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\MiddlewareHandlerContract;
use Chevere\Interfaces\MiddlewareInterface;

class RoleBanned implements MiddlewareInterface
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
