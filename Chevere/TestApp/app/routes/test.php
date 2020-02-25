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

namespace App;

use Chevere\Components\Controller\ControllerName;
use Chevere\Components\Route\Route;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\Wildcard;

return [
    (new Route(new PathUri('/test')))
        ->withAddedMethod(
            new GetMethod,
            new ControllerName(Controllers\Home::class)
        )
        ->withName(new RouteName('test')),
    (new Route(new PathUri('/test/{wildcard}')))
        ->withAddedMethod(
            new GetMethod,
            new ControllerName(Controllers\Home::class)
        )
        ->withAddedWildcard(new Wildcard('wildcard')),
];
