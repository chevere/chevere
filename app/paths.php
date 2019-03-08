<?php
// /**/ significa ruta que se deberia crear automaticamente, sin declaracion previa
return [
    'classes',/**/
    'content',
    'controllers',/**/
    'custom',
    'functions',
    'routes', /**/
    'routes/callables' => ['routes' => 'callables'],/**/
    'system',
    'cache' => ['system' => 'cache'],
    'cache/routes' => ['cache' => 'routes'], /**/
    'cli' => ['system' => 'cli'],
    'sessions' => ['system' => 'sessions'],
    'installer' => ['system' => 'installer'],
    'languages' => ['system' => 'languages'],
    'locks' => ['system' => 'locks'],
    'logs' => ['system' => 'logs'],
    'temp' => ['system' => 'temp'],
    'updater' => ['system' => 'updater'],
    'themes',
    'themes/dashboard' => ['themes' => 'dashboard'],
    'themes/front' => ['themes' => 'front'],
];
