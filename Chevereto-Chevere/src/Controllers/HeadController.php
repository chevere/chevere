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

namespace Chevere\Controllers;

use const Chevere\CLI;
use Chevere\Console\Console;
use Chevere\Controller\Controller;

final class HeadController extends Controller
{
    const OPTIONS = [
        'description' => 'GETT without message-body.',
    ];

    /**
     * Head takes the URI and invokes GET.
     */
    public function __invoke()
    {
        $route = $this->app->route();
        $controller = $route->getController('GET');
        if ($controller) {
            $this->invoke($controller);
            $this->app->response()->setContent(null);
            if (CLI) {
                Console::style()->block($this->app->response()->statusString(), 'STATUS', 'fg=black;bg=green', ' ', true);
            }
        }
    }
}
