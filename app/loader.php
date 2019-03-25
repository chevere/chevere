<?php

namespace Chevereto\Core;

/*
 * La App es un conjunto que resuelve REQUEST -> RESPONSE
 * Este request pasa por un server o es injectado por cli.
 *
 * No se definen variables para no caer en $global.
 *
 * Que pasa si quiero hacer pruebas o lint en requests forgeados?
 * Como se que puedo hacer con la app?
 */

// $appConfig = [
//     'configFiles' => [':config'],
//     'apis' => [
//         'api' => 'apis/api',
//         'api-alt' => 'apis/api-alt',
//     ],
//     'routes' => ['routes:dashboard', 'routes:web'],
// ];

new App(include 'options.php');

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
