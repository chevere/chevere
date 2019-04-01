<?php

// FIXME: ** getControllerObject
declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Controllers;

use const Chevereto\Core\CLI;
use Chevereto\Core\CoreException;
use Chevereto\Core\Console;
use Chevereto\Core\Message;
use Chevereto\Core\Controller;

/**
 * Identical to GET, but without any message-boby in the response.
 */
class ApiHead extends Controller
{
    const OPTIONS = [
        'description' => 'GET without message-body.',
    ];

    public function __invoke()
    {
        $route = $this->getApp()->getRoute();
        $callable = $route->getCallable('GET');

        if ($callable == null) {
            $message =
                (new Message('You have to provide the %s argument when running this callable without route context.'))
                    ->code('%s', 'callable');
            if (CLI) {
                Console::io()->error($message);

                return;
            } else {
                throw new CoreException($message);
            }
        }

        $response = $this->getApp()->getControllerObject($callable);
        $response->setData(null);
    }
}
