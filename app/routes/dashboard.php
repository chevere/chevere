<?php

namespace App;

use Chevere\Http\Method;
use Chevere\Route\Route;

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
