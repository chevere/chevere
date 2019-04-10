<?php

namespace App;

use Chevereto\Core\Route;

return [
  Route::bind('/dashboard/{algo?}', Controllers\Dashboard::class),
  Route::bind('/dashboard/{algo}/{sub}', Controllers\Dashboard::class),
];
