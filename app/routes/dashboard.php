<?php
namespace Chevereto\Core;

Route::bind('/dashboard/{algo}/{sub}', 'callables:dashboard');
