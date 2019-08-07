<?php

namespace App;

use Chevere\Route\Route;

return [
  'index' => (new Route('/', Controllers\Index::class))
    ->setName('homepage')
    ->addMiddleware('middleware:RoleBanned')
    ->addMiddleware('middleware:RoleAdmin'),

  // new Route('/ruta/dos')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/tres')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/cuatro')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/cinco')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/seis')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/siete')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/ocho')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/nueve')->setMethod('GET', Controllers\Index::class),
  // new Route('/ruta/diez')->setMethod('GET', Controllers\Index::class),

  (new Route('/cache/{key?}-{cert}-{user?}'))
    ->setWhere('key', '[0-9]+')
    ->setMethod('GET', Controllers\Cache::class)
    ->setMethod('POST', Controllers\Cache::class)
    ->setName('cache'),

  // new Route('/test/{var0?}-{var1?}-{var2}', Controllers\Index::class),

  // new Route('/{dyn2}')
  //   ->setName('DyN')
  //   ->setMethods([
  //     'GET' => Controllers\Index::class,
  //     'POST' => Controllers\Index::class,
  //   ])
  //   ->setWhere('dyn2', '[0-9]+'),
];
