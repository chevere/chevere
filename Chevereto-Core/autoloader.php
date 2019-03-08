<?php
namespace Chevereto\Core;

/**
 * Chevereto autoloader
 *
 * Provides auto loading for both Chevereto\Core and App namespaces.
 *
 * @see PATH_CLASSES
 * @param string $class Class name.
 */
function autoloader($class)
{
    foreach (NS_HANDLE_LENGTHS as $ns => $len) {
        if (strncmp($ns, $class, $len) !== 0) {
            continue;
        } else {
            $a = 1;
            break;
        }
    }
    if (isset($a) == false) {
        return;
    }
    $relativeClass = str_replace('\\', '/', substr($class, $len));
    $file = constant($ns . 'PATH_CLASSES') . $relativeClass . '.php';
    if (stream_resolve_include_path($file)) {
        include $file;
    }
}
