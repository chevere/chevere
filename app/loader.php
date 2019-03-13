<?php
namespace Chevereto\Core;

use Symfony\Component\EventDispatcher\EventDispatcher;

$dispatcher = new EventDispatcher();
 
// register listener for the 'demo.event' event
$listener = new Listener();
$dispatcher->addListener('demo.event', array($listener, 'onDemoEvent'));

dump($dispatcher);
// dispatch
// $dispatcher->dispatch(Event::NAME, new Event());

die();

/**
 * App initialization
 */
$app = new App();

/**
 * Build the API
 */
$apis = new Apis();
$apis
    ->register('api', 'apis/api')
    ->register('api-alt', 'apis/api-alt');

$app->setApis($apis);

/**
 * Router handles all the App routes (API + explicit) and manages the Routes.
 */
$router = new Router();
$router
    ->register('routes:dashboard')
    ->register('routes:web')
    ->make(); // Router from cache > fly

$app->setRouter($router);

/**
 * Client defines the app user
 */
// $client = new Client();
// $app->setClient($client);

// $apis = App::instance()->getApis(); // ->get('api-alt')

// dump(include 'BACKUP.routing.php');

// Console binds if php_sapi_name = cli
if (Console::bind($app)) {
    Console::run();
} else {
    $app->setRequestFromGlobals();
}

$app->run();
exit();

// Hook::before('deleteUser@api/users:DELETE', function ($that) {
//     // $that->private = 'muahahahaha';
//     $that->source .= ' 1-HOOK-BEFORE-11 ';
// }, 11);

// Hook::before('deleteUser@api/users:DELETE', function ($that) {
//     $that->source .= ' 2-HOOK-BEFORE-11 ';
// }, 11);

// Hook::before('deleteUser@api/users:DELETE', function ($that) {
//     $that->source .= ' HOOK-BEFORE-PN ';
// });
// Hook::before('deleteUser@api/users:DELETE', function ($that) {
//     $that->source .= ' 2HOOK-BEFORE-PN ';
// });

// Hook::after('deleteUser@api/users:DELETE', function ($that) {
//     $that->source .= ' HOOK-AFTER-P5';
// }, 5);


// echo '<pre>' . Utils\Dump::out(Hook::getAll()) . '</pre>';

// dump(Hook::getAll());
