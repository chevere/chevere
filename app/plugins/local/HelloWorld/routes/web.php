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

use App\Controllers\Home;
use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\RouteName;

return [
    (new Route(new PathUri('/hello-world')))
        ->withAddedMethod(
            new Method('GET'),
            new ControllerName(Home::class)
        )
        ->withName(
            new RouteName('plugin.helloWorld')
        ),
];
