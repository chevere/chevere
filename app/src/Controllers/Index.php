<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controllers;

use Chevere\Controller\Controller;
use Chevere\FileReturn;
use Chevere\JsonApi\Data;
use Chevere\Path\Path;
use Chevere\Path\PathHandle;

class Index extends Controller
{
    // /user/{user}/friends/{friend}/comment
    // POST /user/rodolfo/comment --params ...
    // $wildcards = [user => User rodolfo]
    // $parameters = [name => rodolfo, email=> rodolfo@chevereto.com]
    // public function __invoke(array $wildcards, array $parameters)
    public function __invoke()
    {
        throw new \Exception('Ups');
        $api = new Data('api', 'info');
        $api->addAttribute('entry', 'HTTP GET /api');
        $api->addAttribute('description', 'Retrieves the exposed API.');
        // $api->validate();

        $cli = new Data('cli', 'info');
        $cli->addAttribute('entry', 'php app/console list');
        $cli->addAttribute('description', 'Lists console commands.');
        // $api->validate();

        // $this->response = new Response();

        $response = $this->app->response;
        $response->setMeta(['Hello' => 'World!']);
        $response->addData($api);
        $response->addData($cli);
        $response->setStatusCode(200);
    }
}
