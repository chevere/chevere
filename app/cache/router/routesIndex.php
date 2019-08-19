<?php

return array (
  '/api/users' => 
  array (
    'group' => 'api',
    'id' => 'api/users',
  ),
  '/api/users/{user}' => 
  array (
    'group' => 'api',
    'id' => 'api/users/{user}',
  ),
  '/api/users/{user}/friends' => 
  array (
    'group' => 'api',
    'id' => 'api/users/{user}/friends',
  ),
  '/api/users/{user}/relationships/friends' => 
  array (
    'group' => 'api',
    'id' => 'api/users/{user}/relationships/friends',
  ),
  '/api' => 
  array (
    'group' => 'api',
    'id' => 'api',
  ),
  '/dashboard/{algo?}' => 
  array (
    'group' => 'routes:dashboard',
    'id' => '0',
  ),
  '/dashboard/{algo}/{sub}' => 
  array (
    'group' => 'routes:dashboard',
    'id' => '1',
  ),
  '/' => 
  array (
    'group' => 'routes:web',
    'id' => 'index',
  ),
  '/cache/{llave?}-{cert}-{user?}' => 
  array (
    'group' => 'routes:web',
    'id' => '0',
  ),
);