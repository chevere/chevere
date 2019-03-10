<?php
namespace Chevereto\Core;

// FIXME: Esta wea es muy fea!
$pathApp = dirname(dirname(dirname(__DIR__))) . '/app/';

// FIXME: Windows symlinks
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $pathApp = str_replace('\\', '/', dirname(__DIR__) . '/app/');
    $PATH = str_replace('\\', '/', __DIR__ . '/');
} else {
    $PATH = __DIR__ . '/';
}

define(__NAMESPACE__ . '\TIME_BOOTSTRAP', microtime(true));

// Sets core path
define(__NAMESPACE__ . '\PATH', $PATH);
define(__NAMESPACE__ . '\PATH_APP', $pathApp);

// Namespace handles (adds trailing slashes)
const CORE_NS_HANDLE = __NAMESPACE__ . '\\';
const APP_NS_HANDLE = 'App\\';
// Namespace handle lenghts (hard set)
const NS_HANDLE_LENGTHS = [CORE_NS_HANDLE => 15, APP_NS_HANDLE => 4];
// Chevereto\Core classses path
const PATH_CLASSES = PATH . 'src/';
// App classes path
define(APP_NS_HANDLE . 'PATH_CLASSES', $pathApp . 'src/');

// PHP version checker
require PATH . 'utils/phpcheck.php';
// Must-load classes
require PATH . 'src/Dumper.php';
require PATH . 'src/Console.php';

// Init Console only in CLI
if (php_sapi_name() == 'cli') {
    Console::init();
}
// This constant allows safe short syntax like `CLI && Console::io()` in all namespaces.
define('CLI', Console::isAvailable());
// define(APP_NS_HANDLE . 'CLI', Console::isAvailable());
// define(CORE_NS_HANDLE . 'CLI', Console::isAvailable());

// TODO: App autoloader solamente!
require PATH . 'autoloader.php';
spl_autoload_register(CORE_NS_HANDLE . 'autoloader');
