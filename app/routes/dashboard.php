<?php

namespace App;

use Chevere\Components\Http\Method;
use Chevere\Components\Route\Route;

return [
    (new Route('/dashboard/{algo?}'))
        ->withAddedMethod(
            (new Method('GET'))
                ->withController(Controllers\Dashboard::class)
        ),
    (new Route('/dashboard/{algo}/{sub}'))
        ->withAddedMethod(
            (new Method('GET'))
                ->withAddedMethod(Controllers\Dashboard::class)
        ),
];
