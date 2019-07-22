<?php

namespace App;

use Chevere\Route\Route;

return [
  Route::bind('/dashboard/{algo?}', Controllers\Dashboard::class),
  Route::bind('/dashboard/{algo}/{sub}', Controllers\Dashboard::class),
];
