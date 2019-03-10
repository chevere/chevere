<?php
namespace Chevereto\Core;

// if (CLI && Console::io()->confirm('no/yes')) {
//     Console::writeln('<info>You said YES!</>');
// }

// $arr = [
//     'api' => [],
//     'api-alt' => [],
// ];
// $keys = array_keys($arr);
// dd($keys, array_search('api', $keys, true));
// Log::notice('Notice: Miralo nomas...');

// error_reporting(E_ALL);

// dd(Validate::colorHEX('#F_F0000'));

// Create the app
$app = new App();

$apis = new Apis();
$apis
    ->register('api')
    ->register('api-alt');

$app->setApis($apis);

// dd(Routes::instance());

/**
 * Router handles all the App routes (API + explicit) and manages the Routes.
 */
$router = new Router();
$router
    ->register('routes:dashboard')
    ->register('routes:web')
    ->make(); // Router from cache > fly

$app->setRouter($router);

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
