<?php

header('Content-Type: text/plain');

echo '<?php', PHP_EOL;

$dir = 'vendor/chevereto/chevere/src';
$dir = 'app/cache';

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST
);

foreach ($files as $script) {
    $path = $script->getRealPath();
    if ($script->getExtension() == 'php') {
        echo 'opcache_compile_file(', var_export($path, true), ');', PHP_EOL;
    }
}
