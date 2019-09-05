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
use Chevere\JsonApi\Data;

class Index extends Controller
{
    public function __invoke()
    {
        $api = new Data('info', 'api');
        $api->addAttribute('entry', 'HTTP GET /api');
        $api->addAttribute('description', 'Retrieves the exposed API.');

        $cli = new Data('info', 'cli');
        $cli->addAttribute('entry', 'php app/console list');
        $cli->addAttribute('description', 'Retrieves the console command list.');

        $this->document->appendData($api, $cli);
        dd($this->document);
    }
}
