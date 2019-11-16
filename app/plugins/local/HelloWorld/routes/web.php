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

use App\Controllers\Home;
use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\PathUri;

return [
    (new Route(new PathUri('/hello-world')))
        ->withAddedMethod(
            (new Method('GET'))
                ->withControllerName(Home::class)
        )
        ->withName('plugin.helloWorld'),
];
