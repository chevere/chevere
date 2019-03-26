<?php

use Chevereto\Core\Route;

return [Route::bind('/dashboard/{algo}/{sub}', 'callables:dashboard')];
