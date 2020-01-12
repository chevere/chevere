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

namespace Chevere\Components\App\Interfaces;

use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Http\Interfaces\ResponseContract;
use Chevere\Components\Router\Interfaces\RoutedInterface;

interface AppInterface
{
    const FILE_PARAMETERS = 'parameters.php';
    const PATH_LOGS = 'var/logs/';

    public function __construct(ServicesInterface $services, ResponseContract $response);

    /**
     * Return an instance with the specified ServicesContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterContract.
     */
    public function withServices(ServicesInterface $services): AppInterface;

    /**
     * Provides access to the ServicesContract instance.
     */
    public function services(): ServicesInterface;

    /**
     * Return an instance with the specified ResponseContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ResponseContract.
     */
    public function withResponse(ResponseContract $response): AppInterface;

    /**
     * Provides access to the ResponseContract instance.
     */
    public function response(): ResponseContract;

    /**
     * Return an instance with the specified RequestInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RequestInterface.
     */
    public function withRequest(RequestInterface $request): AppInterface;

    /**
     * Returns a boolean indicating whether the instance has a RequestInterface.
     */
    public function hasRequest(): bool;

    /**
     * Provides access to the RequestInterface instance.
     */
    public function request(): RequestInterface;

    /**
     * Return an instance with the specified RoutedContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RoutedContract.
     */
    public function withRouted(RoutedInterface $routed): AppInterface;

    /**
     * Returns a boolean indicating whether the instance has a RoutedContract.
     */
    public function hasRouted(): bool;

    /**
     * Provides access to the RouteContract instance.
     */
    public function routed(): RoutedInterface;

    /**
     * Return an instance with the specified arguments (from controller).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified arguments.
     */
    public function withArguments(array $arguments): AppInterface;

    /**
     * Returns a boolean indicating whether the instance has arguments.
     */
    public function hasArguments(): bool;

    /**
     * Provides access to the application arguments.
     */
    public function arguments(): array;
}
