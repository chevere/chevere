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

use Chevereto\Chevere\Controller\Controller;
use Chevereto\Chevere\JsonApi\Data;

class Index extends Controller
{
    public function __invoke()
    {
        // throw new \Exception('duh');
        $api = new Data('api', 'info');
        $api->addAttribute('entry', 'HTTP GET /api');
        $api->addAttribute('description', 'Retrieves the exposed API.');
        // $api->validate();

        $cli = new Data('cli', 'info');
        $cli->addAttribute('entry', 'php app/console list');
        $cli->addAttribute('description', 'Lists console commands.');
        // $api->validate();

        $this
            ->getResponse()
                ->setMeta(['Hello' => 'World!'])
                ->addData($api)
                ->addData($cli)
                ->setStatusCode(200);
    }
}
