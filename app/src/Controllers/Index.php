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

use Chevereto\Chevere\Controller;
use Chevereto\Chevere\Path;

// use Chevereto\Chevere\PathHandle;

class Index extends Controller
{
    public function __invoke()
    {
        // $res = PathHandle::get('controllers:index');
        $res = Path::fromHandle('controllers:index');
        dump($res);
        die();
        // throw new \Exception('duh');
        $this->getResponse()
            ->setMeta(['Hello' => 'World!'])
            ->addData('info', 'api', ['entry' => 'HTTP GET /api', 'description' => 'Retrieves the exposed API.'])
            ->addData('info', 'cli', ['entry' => 'php app/console list', 'description' => 'Lists console commands.'])
            ->setStatusCode(200);
    }
}
