<?php

namespace App;

use Chevere\Route\Route;
use Chevere\Http\Method;

return [
  'index' => (new Route('/', Controllers\Index::class))
    ->setName('homepage'),
    // ->addMiddleware(Middleware\RoleAdmin::class)
    // ->addMiddleware(Middleware\RoleBanned::class),
  (new Route('/cache/{llave?}-{cert}-{user?}'))
    ->setWhere('llave', '[0-9]+')
    ->setMethod(new Method('GET', Controllers\Cache::class))
    ->setMethod(new Method('POST', Controllers\Cache::class))
    ->setName('cache'),
];
