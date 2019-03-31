<?php

use Chevereto\Core\Route;

return [
  'index' => Route::bind('/', 'callables:index')
      ->name('homepage')
      ->middleware('middleware:RoleBanned')
      ->middleware('middleware:RoleAdmin'),
  Route::bind('/cache/{user?}')
    ->method('GET', 'callables:cache')
    ->method('POST', 'callables:cache')
    ->name('cache'),
  Route::bind('/test/{var0?}-{var1?}-{var2}', 'callables:index'),
  Route::bind('/{dyn2}')
    ->name('DyN')
    ->methods([
      'GET' => 'callables:index',
      'POST' => 'callables:index',
    ])
    ->where('dyn2', '[0-9]+'),
];
