<?php

return array (
  'api' => 
  array (
    'api/users' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Obtiene usuarios.',
          'parameters' => 
          array (
          ),
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
        'OPTIONS' => 
        array (
          'description' => 'Retrieve endpoint OPTIONS.',
        ),
        'HEAD' => 
        array (
          'description' => 'GET without message-body.',
        ),
      ),
    ),
    'api/users/{user}' => 
    array (
      'OPTIONS' => 
      array (
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
        'DELETE' => 
        array (
          'description' => 'Deletes an user.',
          'parameters' => 
          array (
          ),
        ),
        'GET' => 
        array (
          'description' => 'Obtiene un usuario.',
          'parameters' => 
          array (
          ),
        ),
        'OPTIONS' => 
        array (
          'description' => 'Retrieve endpoint OPTIONS.',
        ),
        'HEAD' => 
        array (
          'description' => 'GET without message-body.',
        ),
      ),
      'resource' => 
      array (
        'user' => 
        array (
          'regex' => '[a-z]+',
          'description' => 'Username',
        ),
      ),
    ),
    'api/users/{user}/friends' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Get {user} friends.',
          'parameters' => 
          array (
          ),
        ),
        'OPTIONS' => 
        array (
          'description' => 'Retrieve endpoint OPTIONS.',
        ),
        'HEAD' => 
        array (
          'description' => 'GET without message-body.',
        ),
      ),
      'resource' => 
      array (
        'user' => 
        array (
          'regex' => '[a-z]+',
          'description' => 'Username',
        ),
      ),
    ),
    'api/users/{user}/relationships/friends' => 
    array (
      'OPTIONS' => 
      array (
        'GET' => 
        array (
          'description' => 'Describes endpoint relationship.',
          'parameters' => 
          array (
          ),
        ),
        'OPTIONS' => 
        array (
          'description' => 'Retrieve endpoint OPTIONS.',
        ),
        'HEAD' => 
        array (
          'description' => 'GET without message-body.',
        ),
      ),
    ),
    '' => 
    array (
      'OPTIONS' => 
      array (
        'HEAD' => 
        array (
          'description' => 'GET without message-body.',
          'parameters' => 
          array (
          ),
        ),
        'OPTIONS' => 
        array (
          'description' => 'Retrieve endpoint OPTIONS.',
          'parameters' => 
          array (
          ),
        ),
        'GET' => 
        array (
          'description' => 'Retrieve endpoint.',
          'parameters' => 
          array (
          ),
        ),
      ),
    ),
  ),
);