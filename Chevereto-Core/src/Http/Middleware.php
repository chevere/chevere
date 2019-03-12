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

use function Chevereto\Core\dump;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware provides a way to filter the Http request.
 */
// Restringir acceso a la app (terminate, headers, redirects)
class Middleware
{
    public function process(Request $request, RequestHandler $handler)
    {
        // dump($request->getRealMethod());
        return $handler->handle($request);
        // $response = $handler->handle($request);
    }
}
class Middleware2
{
    public function process(Request $request, RequestHandler $handler)
    {
        $handler
            ->response(Response::HTTP_NOT_FOUND, 'NOT ENCONTRADO');
        // return $handler->handle($request);
    }
}