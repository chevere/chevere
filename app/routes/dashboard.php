<?php

use Chevereto\Core\Route;

return [
  'algo' => Route::bind('/dashboard/{algo?}', 'callables:dashboard'),
  Route::bind('/dashboard/{algo}/{sub}', 'callables:dashboard'),
];
