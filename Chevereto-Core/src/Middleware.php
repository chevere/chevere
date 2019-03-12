<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Exception;
use Closure;

class Middleware
{
    /**
     * Middleware callable.
     */
    public function __invoke(Request $request, Response $response, Closure $next)
    {
        dump($request, $response, $next);
        Console::writeln('Middleware BEFORE');
        return $next($controller);
    }
}
