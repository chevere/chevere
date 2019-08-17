<?php

return array (
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