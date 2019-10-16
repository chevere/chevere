<?php

use App\Controllers\Home;
use Chevere\Components\Route\Route;
use Chevere\Components\Http\Method;

return [
    (new Route('/hello-world'))
        ->withAddedMethod(
            (new Method('GET'))
                ->withControllerName(Home::class)
        )
        ->withName('plugin.helloWorld'),
];
