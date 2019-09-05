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
use Chevere\Data\Data;

class Index extends Controller
{
    public function __invoke()
    {
        $data = new Data();
        $data->append([
            'type'          => 'info',
            'id'            => 'api',
            'attributes'    => [
                'entry' => 'HTTP GET /api',
                'description' => 'Retrieves the exposed API.',
            ],
        ]);
        $data->append([
            'type'          => 'info',
            'id'            => 'cli',
            'attributes'    => [
                'entry' => 'php app/console list',
                'description' => 'Lists console commands.',
            ],
        ]);
        dd($data->toArray());

        $this->setData($data);
    }
}
