<?php

namespace App;

use Chevereto\Chevere\Route\Route;

return [
  'index' => Route::bind('/', Controllers\Index::class)
      ->setName('homepage')
      ->addMiddleware('middleware:RoleBanned')
      ->addMiddleware('middleware:RoleAdmin'),

  Route::bind('/cache/{user?}')
    ->setMethod('GET', Controllers\Cache::class)
    ->setMethod('POST', Controllers\Cache::class)
    ->setName('cache'),

  Route::bind('/test/{var0?}-{var1?}-{var2}', Controllers\Index::class),

  Route::bind('/{dyn2}')
    ->setName('DyN')
    ->setMethods([
      'GET' => Controllers\Index::class,
      'POST' => Controllers\Index::class,
    ])
    ->setWhere('dyn2', '[0-9]+'),
];
