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

namespace App;

use Chevere\App\App;
use Chevere\Interfaces\HandlerInterface;
use Chevere\Interfaces\MiddlewareInterface;

return new class () implements MiddlewareInterface
{
    public function __invoke(App $app, HandlerInterface $handler)
    {
        // \dump(__FILE__);
        // $userRole = 'user';
        // if ('banned' == $userRole) {
        //     return $handler->stop($app);
        // }
        return $handler->process($app);
    }
};
