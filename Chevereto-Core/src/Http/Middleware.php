<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core\Http;

use Exception;
use Closure;

use Chevereto\Core\Console;
use function Chevereto\Core\dump;

/**
 * Middleware provides a way to filter the Http request.
 */
// Restringir acceso a la app (terminate, headers, redirects)
class Middleware
{
    // Before REQUEST middleware
    public function __invoke(Request $request, closure $next)
    {
        Console::log('Peform action...');
        return $next($request);
    }
    // After REQUEST middleware
    // public function __invoke(Request $request, closure $next)
    // {
    //     $response = $next($request);
    //     Console::log('Peform action...');
    //     return $response;
    // }
}