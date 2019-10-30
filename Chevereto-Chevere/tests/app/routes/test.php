<?php

namespace App;

use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;

return [
    (new Route('/test'))
        ->withAddedMethod(
            (new Method('GET'))
                ->withControllerName(Controllers\Home::class)
        )
        ->withName('test')
];
