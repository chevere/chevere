<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\App;

use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

use const Chevere\APP_PATH;

interface AppContract
{
    const FILE_PARAMETERS = 'parameters.php';
    const PATH_LOGS = APP_PATH . 'var/logs/';

    public function __construct(ResponseContract $response);

    /**
     * Return an instance with the specified response.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified response.
     */
    public function withResponse(ResponseContract $response): AppContract;
    
    public function hasResponse(): bool;

    public function response(): ResponseContract;

    /**
     * Return an instance with the specified response.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified route.
     */
    public function withRoute(RouteContract $route): AppContract;
    
    public function hasRoute(): bool;
    
    public function route(): RouteContract;
    
    /**
     * Return an instance with the specified arguments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified router.
     */
    public function withRouter(RouterContract $router): AppContract;
    
    public function hasRouter(): bool;
    
    public function router(): RouterContract;

    /**
     * Return an instance with the specified arguments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified arguments.
     */
    public function withArguments(array $arguments): AppContract;

    public function hasArguments(): bool;
    
    public function arguments(): array;
}
