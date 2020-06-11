<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Filesystem\FileFromString;
use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Writers\StreamWriter;
use Chevere\Components\Writers\Writers;
use Chevere\Examples\HelloWorldController;
use Laminas\Diactoros\Stream;
use function Chevere\Components\Writers\writers;

require 'vendor/autoload.php';

$file = new FileFromString(__DIR__ . '/3.writer.php.log');
if (!$file->exists()) {
    $file->create();
}
new WritersInstance(
    (new Writers)
        ->withOut(
            new StreamWriter(
                new Stream($file->path()->absolute(), 'w')
            )
        )
);
$controller = new HelloWorldController;
$arguments = new ControllerArguments(
    $controller->parameters(),
    ['name' => 'World']
);
$runner = new ControllerRunner($controller);
$ran = $runner->ran($arguments);
writers()->out()->write(implode(' ', $ran->data()));

// Hello, World @ 3.writer.log
