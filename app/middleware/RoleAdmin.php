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

use Chevereto\Chevere\App\App;
use Chevereto\Chevere\Interfaces\HandlerInterface;
use Chevereto\Chevere\Interfaces\MiddlewareInterface;

return new class() implements MiddlewareInterface {
    public function __invoke(App $app, HandlerInterface $handler)
    {
        // \dump(__FILE__);
        // \dump($app->arguments;
        // $userRole = $app->getUser()->role;
        // $userRole = 'user';
        // if ('admin' != $userRole) {
        //     // return $handler->stop();
        // }
        return $handler->process($app);
    }
};
