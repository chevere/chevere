<?php

namespace App;

use Chevere\Route\Route;

return [
  new Route('/dashboard/{algo?}', Controllers\Dashboard::class),
  new Route('/dashboard/{algo}/{sub}', Controllers\Dashboard::class),
];
