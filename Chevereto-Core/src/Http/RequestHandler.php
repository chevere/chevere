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

use Symfony\Component\HttpFoundation\Response;

class RequestHandler
{
    protected $queue;
    // Used to set the queue
    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }
    // Used to be called from Middleware fn
    public function runner($request)
    {
        reset($this->queue);
        return $this->handle($request);
    }
    // Middleware handler
    public function handle($request)
    {
        if ($middleware = current($this->queue)) {
            next($this->queue);
            return $middleware->process($request, $this);
        }
    }
    public function response(int $http_code, string $content = null, array $headers = [])
    {
        $response = new Response($content, $http_code, $headers);
    }
}