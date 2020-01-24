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

namespace Chevere\Components\Controllers;

use Chevere\Components\Controller\Controller;
use Chevere\Components\Http\Method;

final class HeadController extends Controller
{
    const OPTIONS = [
        'description' => 'GETT without message-body.',
    ];

    /**
     * Head takes the URI and invokes GET.
     */
    public function __invoke(): void
    {
        // $route = $this->app()->route();
        // $controller = $route->controllerName(new Method('GET'))->toString();
        // if ($controller) {
        //     $this->invoke($controller);
        //     // $this->app()->response()->setContent(null);
        //     // if (CLI) {
        //     //     Console::style()->block($this->app()->response()->statusString(), 'STATUS', 'fg=black;bg=green', ' ', true);
        //     // }
        // }
    }

    public function getContent(): string
    {
        return '';
    }
}
