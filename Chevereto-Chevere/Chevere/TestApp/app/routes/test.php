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

namespace App;

use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;
use Chevere\Components\Route\PathUri;

return [
    (new Route(new PathUri('/test')))
        ->withAddedMethod(
            (new Method('GET'))
                ->withControllerName(Controllers\Home::class)
        )
        ->withName('test'),
];
