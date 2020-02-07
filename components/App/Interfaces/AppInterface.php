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

namespace Chevere\Components\App\Interfaces;

use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Http\Interfaces\ResponseInterface;
use Chevere\Components\Router\Interfaces\RoutedInterface;

interface AppInterface
{
    const FILE_PARAMETERS = 'parameters.php';
    const PATH_LOGS = 'var/logs/';

    public function __construct(ServicesInterface $services, ResponseInterface $response);

    /**
     * Return an instance with the specified ServicesInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterInterface.
     */
    public function withServices(ServicesInterface $services): AppInterface;

    /**
     * Provides access to the ServicesInterface instance.
     */
    public function services(): ServicesInterface;

    /**
     * Return an instance with the specified ResponseInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ResponseInterface.
     */
    public function withResponse(ResponseInterface $response): AppInterface;

    /**
     * Provides access to the ResponseInterface instance.
     */
    public function response(): ResponseInterface;

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
     * Return an instance with the specified RoutedInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RoutedInterface.
     */
    public function withRouted(RoutedInterface $routed): AppInterface;

    /**
     * Returns a boolean indicating whether the instance has a RoutedInterface.
     */
    public function hasRouted(): bool;

    /**
     * Provides access to the RouteInterface instance.
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
