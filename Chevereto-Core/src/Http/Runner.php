<?php
declare(strict_types=1);
namespace Chevereto\Core\Http;

class Runner
{
    /** @var callable[] */
    protected $queue;
    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }
    public function __invoke($request, $response)
    {
        $middleware = array_shift($this->queue);
        if (null === $middleware) {
            // Default
            return function ($request, $response, callable $next) {
                return $response;
            };
        }
        return $middleware($request, $response, $this);
    }
}