<?php

return array (
  'GROUP' => 
  array (
    2 => 
    array (
      'static' => 
      array (
        '^/api/users$' => '/api/users',
      ),
      'mixed' => 
      array (
        'user' => 
        array (
          '^/user/([A-z0-9\\.\\-\\%]+)$' => '/user/{user}',
        ),
      ),
    ),
    3 => 
    array (
      'mixed' => 
      array (
        'api' => 
        array (
          '^/api/users/([A-z0-9\\.\\-\\%]+)$' => '/api/users/{user}',
        ),
        'user' => 
        array (
          '^/user/([A-z0-9\\.\\-\\%]+)/delete$' => '/user/{user}/delete',
        ),
        'dashboard' => 
        array (
          '^/dashboard/([A-z0-9\\.\\-\\%]+)/([A-z0-9\\.\\-\\%]+)$' => '/dashboard/{algo}/{sub}',
        ),
      ),
    ),
    4 => 
    array (
      'mixed' => 
      array (
        'api' => 
        array (
          '^/api/users/([A-z0-9\\.\\-\\%]+)/friends$' => '/api/users/{user}/friends',
        ),
      ),
    ),
    5 => 
    array (
      'mixed' => 
      array (
        'api' => 
        array (
          '^/api/users/([A-z0-9\\.\\-\\%]+)/friends/([A-z0-9\\.\\-\\%]+)$' => '/api/users/{user}/friends/{friend}',
        ),
      ),
    ),
    6 => 
    array (
      'mixed' => 
      array (
        'api' => 
        array (
          '^/api/users/([A-z0-9\\.\\-\\%]+)/friends/([A-z0-9\\.\\-\\%]+)/lovers$' => '/api/users/{user}/friends/{friend}/lovers',
        ),
      ),
    ),
    7 => 
    array (
      'mixed' => 
      array (
        'api' => 
        array (
          '^/api/users/([A-z0-9\\.\\-\\%]+)/friends/([A-z0-9\\.\\-\\%]+)/lovers/([A-z0-9\\.\\-\\%]+)$' => '/api/users/{user}/friends/{friend}/lovers/{lover}',
        ),
      ),
    ),
    0 => 
    array (
      'static' => 
      array (
        '^/$' => '/',
      ),
    ),
    1 => 
    array (
      'static' => 
      array (
        '^/cache$' => '/cache',
        '^/user$' => '/user',
      ),
    ),
  ),
  'FLAT' => 
  array (
    '/api/users' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/GET.php',
        'POST' => 'app/api/users/POST.php',
      ),
      'components' => 
      array (
        0 => 'api',
        1 => 'users',
      ),
      'count' => 2,
      'wildcards' => 
      array (
      ),
      'type' => 'static',
      'regex' => '^/api/users$',
      'maker' => 
      array (
        'filename' => 'app/routes/api.php',
        'line' => 4,
        'method' => 'Chevereto\\Core\\Router::bindAutoAPI',
      ),
      'renamed' => NULL,
    ),
    '/api/users/{user}' => 
    array (
      'methods' => 
      array (
        'DELETE' => 'app/api/users/DELETE.php',
        'GET' => 'app/api/users/GET.php',
        'PATCH' => 'app/api/users/PATCH.php',
      ),
      'components' => 
      array (
        0 => 'api',
        1 => 'users',
        2 => '{user}',
      ),
      'count' => 3,
      'wildcards' => 
      array (
        0 => 'user',
      ),
      'type' => 'mixed',
      'regex' => '^/api/users/([A-z0-9\\.\\-\\%]+)$',
      'maker' => 
      array (
        'filename' => 'app/routes/api.php',
        'line' => 4,
        'method' => 'Chevereto\\Core\\Router::bindAutoAPI',
      ),
      'renamed' => NULL,
    ),
    '/api/users/{user}/friends' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/friends/GET.php',
      ),
      'components' => 
      array (
        0 => 'api',
        1 => 'users',
        2 => '{user}',
        3 => 'friends',
      ),
      'count' => 4,
      'wildcards' => 
      array (
        0 => 'user',
      ),
      'type' => 'mixed',
      'regex' => '^/api/users/([A-z0-9\\.\\-\\%]+)/friends$',
      'maker' => 
      array (
        'filename' => 'app/routes/api.php',
        'line' => 4,
        'method' => 'Chevereto\\Core\\Router::bindAutoAPI',
      ),
      'renamed' => NULL,
    ),
    '/api/users/{user}/friends/{friend}' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/friends/GET.php',
      ),
      'components' => 
      array (
        0 => 'api',
        1 => 'users',
        2 => '{user}',
        3 => 'friends',
        4 => '{friend}',
      ),
      'count' => 5,
      'wildcards' => 
      array (
        0 => 'user',
        1 => 'friend',
      ),
      'type' => 'mixed',
      'regex' => '^/api/users/([A-z0-9\\.\\-\\%]+)/friends/([A-z0-9\\.\\-\\%]+)$',
      'maker' => 
      array (
        'filename' => 'app/routes/api.php',
        'line' => 4,
        'method' => 'Chevereto\\Core\\Router::bindAutoAPI',
      ),
      'renamed' => NULL,
    ),
    '/api/users/{user}/friends/{friend}/lovers' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/friends/lovers/GET.php',
      ),
      'components' => 
      array (
        0 => 'api',
        1 => 'users',
        2 => '{user}',
        3 => 'friends',
        4 => '{friend}',
        5 => 'lovers',
      ),
      'count' => 6,
      'wildcards' => 
      array (
        0 => 'user',
        1 => 'friend',
      ),
      'type' => 'mixed',
      'regex' => '^/api/users/([A-z0-9\\.\\-\\%]+)/friends/([A-z0-9\\.\\-\\%]+)/lovers$',
      'maker' => 
      array (
        'filename' => 'app/routes/api.php',
        'line' => 4,
        'method' => 'Chevereto\\Core\\Router::bindAutoAPI',
      ),
      'renamed' => NULL,
    ),
    '/api/users/{user}/friends/{friend}/lovers/{lover}' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/friends/lovers/GET.php',
      ),
      'components' => 
      array (
        0 => 'api',
        1 => 'users',
        2 => '{user}',
        3 => 'friends',
        4 => '{friend}',
        5 => 'lovers',
        6 => '{lover}',
      ),
      'count' => 7,
      'wildcards' => 
      array (
        0 => 'user',
        1 => 'friend',
        2 => 'lover',
      ),
      'type' => 'mixed',
      'regex' => '^/api/users/([A-z0-9\\.\\-\\%]+)/friends/([A-z0-9\\.\\-\\%]+)/lovers/([A-z0-9\\.\\-\\%]+)$',
      'maker' => 
      array (
        'filename' => 'app/routes/api.php',
        'line' => 4,
        'method' => 'Chevereto\\Core\\Router::bindAutoAPI',
      ),
      'renamed' => NULL,
    ),
    '/' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/controllers/index.php',
        'POST' => 'app/controllers/index.php',
      ),
      'components' => 
      array (
      ),
      'count' => 0,
      'wildcards' => 
      array (
      ),
      'type' => 'static',
      'regex' => '^/$',
      'maker' => 
      array (
        'filename' => 'app/routes/web.php',
        'line' => 5,
        'method' => 'Chevereto\\Core\\Router::bind',
      ),
      'renamed' => NULL,
    ),
    '/cache' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/controllers/cache.php',
      ),
      'components' => 
      array (
        0 => 'cache',
      ),
      'count' => 1,
      'wildcards' => 
      array (
      ),
      'type' => 'static',
      'regex' => '^/cache$',
      'maker' => 
      array (
        'filename' => 'app/routes/web.php',
        'line' => 6,
        'method' => 'Chevereto\\Core\\Router::bind',
      ),
      'renamed' => NULL,
    ),
    '/user' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/GET.php',
      ),
      'components' => 
      array (
        0 => 'user',
      ),
      'count' => 1,
      'wildcards' => 
      array (
      ),
      'type' => 'static',
      'regex' => '^/user$',
      'maker' => 
      array (
        'filename' => 'Chevereto-Core/src/Router.php',
        'line' => 611,
        'method' => 'Chevereto\\Core\\Router::bind',
      ),
      'renamed' => NULL,
    ),
    '/user/{user}' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/GET.php',
      ),
      'components' => 
      array (
        0 => 'user',
        1 => '{user}',
      ),
      'count' => 2,
      'wildcards' => 
      array (
        0 => 'user',
      ),
      'type' => 'mixed',
      'regex' => '^/user/([A-z0-9\\.\\-\\%]+)$',
      'maker' => 
      array (
        'filename' => 'app/routes/web.php',
        'line' => 13,
        'method' => 'Chevereto\\Core\\Router::bind',
      ),
      'renamed' => NULL,
    ),
    '/user/{user}/delete' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/api/users/DELETE.php',
      ),
      'components' => 
      array (
        0 => 'user',
        1 => '{user}',
        2 => 'delete',
      ),
      'count' => 3,
      'wildcards' => 
      array (
        0 => 'user',
      ),
      'type' => 'mixed',
      'regex' => '^/user/([A-z0-9\\.\\-\\%]+)/delete$',
      'maker' => 
      array (
        'filename' => 'app/routes/web.php',
        'line' => 15,
        'method' => 'Chevereto\\Core\\Router::bind',
      ),
      'renamed' => NULL,
    ),
    '/dashboard/{algo}/{sub}' => 
    array (
      'methods' => 
      array (
        'GET' => 'app/controllers/dashboard.php',
      ),
      'components' => 
      array (
        0 => 'dashboard',
        1 => '{algo}',
        2 => '{sub}',
      ),
      'count' => 3,
      'wildcards' => 
      array (
        0 => 'algo',
        1 => 'sub',
      ),
      'type' => 'mixed',
      'regex' => '^/dashboard/([A-z0-9\\.\\-\\%]+)/([A-z0-9\\.\\-\\%]+)$',
      'maker' => 
      array (
        'filename' => 'app/routes/dashboard.php',
        'line' => 5,
        'method' => 'Chevereto\\Core\\Router::bind',
      ),
      'renamed' => NULL,
    ),
  ),
  'API' => 
  array (
    'users' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Retrieves users.',
        ),
        'POST' => 
        array (
          'description' => 'Creates an user.',
          'parameters' => 
          array (
            'username' => 
            array (
              'description' => 'Username.',
            ),
            'email' => 
            array (
              'description' => 'User email.',
            ),
          ),
        ),
      ),
    ),
    'users/{user}' => 
    array (
      'OPTIONS' => 
      array (
        'DELETE' => 
        array (
          'description' => 'Deletes an user.',
        ),
        'GET' => 
        array (
          'description' => 'Retrieves an user.',
        ),
        'PATCH' => 
        array (
          'description' => 'Updates an user.',
          'parameters' => 
          array (
            'email' => 
            array (
              'description' => 'User email.',
            ),
          ),
        ),
      ),
      'wildcards' => 
      array (
        'user' => 
        array (
          'description' => 'User id.',
          'regex' => '[0-9]+',
        ),
      ),
    ),
    'users/{user}/friends' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Retrieves user friends.',
        ),
      ),
      'wildcards' => 
      array (
        'user' => 
        array (
          'description' => 'User ide.',
          'regex' => '[0-9]+',
        ),
      ),
    ),
    'users/{user}/friends/{friend}' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Retrieves user friend.',
        ),
      ),
      'wildcards' => 
      array (
        'user' => 
        array (
          'description' => 'User ide.',
          'regex' => '[0-9]+',
        ),
        'friend' => 
        array (
          'description' => 'Friend ide.',
          'regex' => '[0-9]+',
        ),
      ),
    ),
    'users/{user}/friends/{friend}/lovers' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Retrieves an user+friend lovers.',
        ),
      ),
      'wildcards' => 
      array (
        'user' => 
        array (
          'description' => 'User ide.',
          'regex' => '[0-9]+',
        ),
        'friend' => 
        array (
          'description' => 'Friend ide.',
          'regex' => '[0-9]+',
        ),
      ),
    ),
    'users/{user}/friends/{friend}/lovers/{lover}' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Retrieves a user+friend lover.',
        ),
      ),
      'wildcards' => 
      array (
        'user' => 
        array (
          'description' => 'User ide.',
          'regex' => '[0-9]+',
        ),
        'friend' => 
        array (
          'description' => 'Friend ide.',
          'regex' => '[0-9]+',
        ),
        'lover' => 
        array (
          'description' => 'Lover ide.',
          'regex' => '[0-9]+',
        ),
      ),
    ),
  ),
);