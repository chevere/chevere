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

namespace Chevereto\Chevere\Controllers;

use Chevereto\Chevere\Controller;

class Head extends Controller
{
    const OPTIONS = [
        'description' => 'GETT without message-body.',
    ];

    /**
     * Head takes the URI and invokes GET.
     */
    public function __invoke()
    {
        $route = $this->getApp()->getRoute();
        $routeUri = $route->getUri();
        dx($routeUri);
        $invoke = $this->invoke(__NAMESPACE__.'\ApiGet', ...func_get_args());
        $invoke->setContent(null);

        return $invoke;
    }
}
