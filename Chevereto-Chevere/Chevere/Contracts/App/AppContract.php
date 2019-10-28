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

use Chevere\Contracts\Http\RequestContract;
use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Contracts\Router\RouterContract;

use const Chevere\APP_PATH;

interface AppContract
{
    const FILE_PARAMETERS = 'parameters.php';
    const PATH_LOGS = APP_PATH . 'var/logs/';

    /**
     * Construct the application container.
     * An application container always have a ResponseContract attached.
     */
    public function __construct(ResponseContract $response);

    /**
     * Return an instance with the specified RequestContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RequestContract.
     */
    public function withRequest(RequestContract $request): AppContract;

    /**
     * Returns a boolean indicating whether the instance has a RequestContract.
     */
    public function hasRequest(): bool;

    /**
     * Provides access to the RequestContract instance.
     */
    public function request(): RequestContract;

    /**
     * Return an instance with the specified ResponseContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ResponseContract.
     */
    public function withResponse(ResponseContract $response): AppContract;

    /**
     * Provides access to the ResponseContract instance.
     */
    public function response(): ResponseContract;

    /**
     * Return an instance with the specified RouteContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouteContract.
     */
    public function withRoute(RouteContract $route): AppContract;
    
    /**
     * Returns a boolean indicating whether the instance has a RouteContract.
     */
    public function hasRoute(): bool;
    
    /**
     * Provides access to the RouteContract instance.
     */
    public function route(): RouteContract;
    
    /**
     * Return an instance with the specified RouterContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterContract.
     */
    public function withRouter(RouterContract $router): AppContract;
    
    /**
     * Returns a boolean indicating whether the instance has a RouterContract.
     */
    public function hasRouter(): bool;
    
    /**
     * Provides access to the RouterContract instance.
     */
    public function router(): RouterContract;

    /**
     * Return an instance with the specified arguments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified arguments.
     */
    public function withArguments(array $arguments): AppContract;

    /**
     * Returns a boolean indicating whether the instance has arguments.
     */
    public function hasArguments(): bool;
    
    /**
     * Provides access to the application arguments.
     */
    public function arguments(): array;
}
