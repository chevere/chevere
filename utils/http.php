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
/**
 * @var Goridge\RelayInterface $relay
 */
use Spiral\Goridge;
use Spiral\RoadRunner;

ini_set('display_errors', 'stderr');
require 'vendor/autoload.php';

$worker = new RoadRunner\Worker(new Goridge\StreamRelay(STDIN, STDOUT));
$psr7 = new RoadRunner\PSR7Client($worker);

while ($req = $psr7->acceptRequest()) {
    try {
        ob_start();
        phpinfo();
        $data = ob_get_contents();
        ob_clean();
        $resp = new \Zend\Diactoros\Response();
        $resp->getBody()->write($data);

        $psr7->respond($resp);
    } catch (\Throwable $e) {
        $psr7->getWorker()->error((string)$e);
    }
}
