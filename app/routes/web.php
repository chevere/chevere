<?php

use Chevereto\Core\Route;

return [
  'index' => Route::bind('/', 'callables:index')
      ->setName('homepage')
      ->addMiddleware('middleware:RoleBanned')
      ->addMiddleware('middleware:RoleAdmin'),
  Route::bind('/cache/{user?}')
    ->setMethod('GET', 'callables:cache')
    ->setMethod('POST', 'callables:cache')
    ->setName('cache'),
  Route::bind('/test/{var0?}-{var1?}-{var2}', 'callables:index'),
  Route::bind('/{dyn2}')
    ->setName('DyN')
    ->setMethods([
      'GET' => 'callables:index',
      'POST' => 'callables:index',
    ])
    ->setWhere('dyn2', '[0-9]+'),
];
