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

namespace Chevere\Components\App\Contracts;

use Chevere\Components\Http\Contracts\RequestContract;
use Chevere\Components\Http\Contracts\ResponseContract;
use Chevere\Components\Router\Contracts\RoutedContract;

interface AppContract
{
    const FILE_PARAMETERS = 'parameters.php';
    const PATH_LOGS = 'var/logs/';

    public function __construct(ServicesContract $services, ResponseContract $response);

    /**
     * Return an instance with the specified ServicesContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterContract.
     */
    public function withServices(ServicesContract $services): AppContract;

    /**
     * Provides access to the ServicesContract instance.
     */
    public function services(): ServicesContract;

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
     * Return an instance with the specified RoutedContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RoutedContract.
     */
    public function withRouted(RoutedContract $routed): AppContract;

    /**
     * Returns a boolean indicating whether the instance has a RoutedContract.
     */
    public function hasRouted(): bool;

    /**
     * Provides access to the RouteContract instance.
     */
    public function routed(): RoutedContract;

    /**
     * Return an instance with the specified arguments (from controller).
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
