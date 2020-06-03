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

use Chevere\Components\Bootstrap\Bootstrap;
use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Controller\ControllerRunner;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Instances\WritersInstance;
use Chevere\Components\Writers\Writers;
use Chevere\TestApp\App\Controllers\TestController;
use Ds\Map;
use Spiral\Goridge;
use Spiral\RoadRunner;
use Zend\Diactoros\Response;

ini_set('display_errors', 'stderr');
require 'vendor/autoload.php';

$roadRunnerWorker = new RoadRunner\Worker(new Goridge\StreamRelay(STDIN, STDOUT));
$psr7 = new RoadRunner\PSR7Client($roadRunnerWorker);

$rootDir = new Dir(new Path(__DIR__ . '/Chevere/TestApp/'));

new BootstrapInstance(
    (new Bootstrap($rootDir))
        ->withCli(true)
);

new WritersInstance(new Writers());

while ($serverRequest = $psr7->acceptRequest()) {
    try {
        $controller = new TestController;
        $runner = new ControllerRunner($controller);
        $controllerArguments = new ControllerArguments(
            $controller->parameters(),
            new Map(['name' => 'PeterPoison', 'id' => '123'])
        );
        $ran = $runner->ran($controllerArguments);
        $response = new Response();
        $response->getBody()->write(json_encode($ran->data()));

        $psr7->respond($response);
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);
    }
}
