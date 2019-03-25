<?php

namespace Chevereto\Core;

/**
 * Chevereto autoloader.
 *
 * Provides auto loading for both Chevereto\Core and App namespaces.
 *
 * @see PATH_CLASSES
 *
 * @param string $class class name
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
    $file = ROOT_PATH.DIRECTORY_SEPARATOR.constant(CORE_NS_HANDLE.($ns == 'App\\' ? 'App\\' : null).'PATH').DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.$relativeClass.'.php';
    if (stream_resolve_include_path($file)) {
        include $file;
    }
}
