<?php

namespace Chevereto\Core;

// Add route:
Route::bind('/', 'callables:index')
  ->name('homepage')
  ->middleware('middleware:RoleBanned')
  ->middleware('middleware:RoleAdmin');

Route::bind('/cache/{user?}')
  ->method('GET', 'callables:cache')
  ->method('POST', 'callables:cache')
  ->name('cache');

Route::bind('/test/{var0?}-{var1?}-{var2}', 'callables:index');

// Get route:
// Route::get('homepage')
//   ->method('POST', 'callables:postComments');

// Remove route (using /route/path):
// Route::unbind('/');

// Remove route (specified by its id or name)
// Route::remove('homepage');

Route::bind('/ruta/{opt1?}-wea/{opt2?}.mas/{fixed1}/{optional?}')
  ->name('dyn_route_1')
  ->method('GET', 'callables:index')
  ->method('POST', 'callables:index')
  ->where('opt1', '[optrex]+')
  ->where('fixed1', '[fixrex]+');

Route::bind('/{dyn2}')
  ->name('dyn_route_2')
  ->methods([
    'GET' => 'callables:index',
    'POST' => 'callables:index',
  ])
  ->where('dyn2', '[0-9]+');

Route::bind('/estatica-wea/mas', 'callables:index');
Route::bind('/{dyn3}', 'callables:index');

Route::bind('/{dyn4}')
  ->method('POST', 'callables:index');
// Route::bind('/saco/{wea}', 'routes/callables:index');
// Sub-ruta con Clase anonima
// Route::bind('/sub/{route?}', 'routes/callables:sub-ruta')->where(['route' => '[a-z]+']);

// Route::bind('/posts/{post}/comments/{comment}', 'routes/callables:postComments');

// Route::bind('/user/{user?}', 'api/users:GET');
// Route::bind('/user/{user}/delete', 'api/users:DELETE', ['GET']);

// Route::bind('/{wildcard}', 'routes/callables:index');

// Route::bind('/view/profile/{id}', 'user@get');
//--> get(['id' => {id}])
//
// Route::bind('/sub/{ruta}', function() {
//   echo 'Hola sub/{ruta}!';
// })->where(['ruta' => '[0-9]+']);
// Route::bind('/seb/{alt}', function() {
//   echo 'Hola seb/{alt}!';
// });
// Route::bind('/{dyn}/{dyn}', function() {
//   echo 'Hola {dyn}/{dyn}!';
// })->where(['dyn' => '[0-9]+']);
// Route::bind('/{user}', function() {
//   echo 'Hola {user}!';
// });
// Route::bind('/{user}/get/{friend}', function() {
//   echo 'Hola {user}/get/{friend}!';
// });
// Route::bind('/ehto', function() {
//   echo 'Hola ehto!';
// });

// Route::bind('/{wea}/{weo}', function () {
//     echo $algo;
// }, 'GET')->where(['wea' => '[a-z]+', 'weo' => '[0-9]+']);
// Route:: bind('/user/{name}', function ($name) {
//     $wea = $name;
//     $fn = function () use ($wea) {
//         echo $wea;
//     };
//     $fn();
// }, 'POST')->where(['name' => '[A-z]+']);
// Route::bind('/{wildcard}', function () {});
// Route::bind('/profile/edit', 'Login:getUser');
// Route::bind('/avatar', 'extend/godlike/reactions/User:getAvatar');
// Route::bind('/{profile}/{id}/delete', function () use ($esta) {
//     echo 'cosito --' . $esta . '-- ';
// });
// Route::bind('/profile/view/{id}', function () {});
// Route::bind('/profile/{id}/delete', function () {})::bind('/otra', 'call');
// Route::bind('friends/{name}/{id}', function ($id) {
//     echo 'Friend Id #' . $id;
// }, ['GET', 'POST'])->where(['id' => '[0-9]+', 'name' => '[a-z]+']);
// Route::bind('/friends/{id}', 'User:get', ['GET', 'OPTIONS']);
// Route::unbind('/otra');
