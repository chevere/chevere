<?php

namespace App;

use Chevere\Route\Route;

return [
  'index' => Route::bind('/', Controllers\Index::class)
    ->setName('homepage')
    ->addMiddleware('middleware:RoleBanned')
    ->addMiddleware('middleware:RoleAdmin'),

  // Route::bind('/ruta/dos')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/tres')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/cuatro')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/cinco')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/seis')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/siete')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/ocho')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/nueve')->setMethod('GET', Controllers\Index::class),
  // Route::bind('/ruta/diez')->setMethod('GET', Controllers\Index::class),

  Route::bind('/cache/{key?}-{cert}-{user?}')
    ->setWhere('key', '[0-9]+')
    ->setMethod('GET', Controllers\Cache::class)
    ->setMethod('POST', Controllers\Cache::class)
    ->setName('cache'),

  // Route::bind('/test/{var0?}-{var1?}-{var2}', Controllers\Index::class),

  // Route::bind('/{dyn2}')
  //   ->setName('DyN')
  //   ->setMethods([
  //     'GET' => Controllers\Index::class,
  //     'POST' => Controllers\Index::class,
  //   ])
  //   ->setWhere('dyn2', '[0-9]+'),
];
