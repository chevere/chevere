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

namespace Chevere\Interfaces\Route;

use Chevere\Components\Http\Methods\ConnectMethod;
use Chevere\Components\Http\Methods\DeleteMethod;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\HeadMethod;
use Chevere\Components\Http\Methods\OptionsMethod;
use Chevere\Components\Http\Methods\PatchMethod;
use Chevere\Components\Http\Methods\PostMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Http\Methods\TraceMethod;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Action\ControllerInterface;
use Chevere\Interfaces\Description\DescriptionInterface;
use Chevere\Interfaces\Http\MethodInterface;

/**
 * Describes the component in charge of defining a route endpoint.
 *
 * Note: Parameters must be automatically determined from known `$controller` parameters.
 */
interface RouteEndpointInterface extends DescriptionInterface
{
    /** Known HTTP methods */
    public const KNOWN_METHODS = [
        'CONNECT' => ConnectMethod::class,
        'DELETE' => DeleteMethod::class,
        'GET' => GetMethod::class,
        'HEAD' => HeadMethod::class,
        'OPTIONS' => OptionsMethod::class,
        'PATCH' => PatchMethod::class,
        'POST' => PostMethod::class,
        'PUT' => PutMethod::class,
        'TRACE' => TraceMethod::class,
    ];

    public function __construct(
        MethodInterface $method,
        ControllerInterface $controller
    );

    /**
     * Provides access to the `$method` instance.
     */
    public function method(): MethodInterface;

    /**
     * Provides access to the `$controller` instance.
     */
    public function controller(): ControllerInterface;

    /**
     * Return an instance with the specified `$description`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$description`.
     */
    public function withDescription(string $description): RouteEndpointInterface;

    /**
     * Return an instance with the specified `$parameter` removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$parameter` removed.
     *
     * @throws OutOfBoundsException
     */
    public function withoutParameter(string $parameter): RouteEndpointInterface;

    /**
     * Provides access to the parameters.
     *
     * ```php
     * return [
     *     'name' => [
     *         'name' => 'name',
     *         'regex' => '/^\w+$/',
     *         'description' => 'User name',
     *         'isRequired' => true,
     *     ],
     * ];
     * ```
     */
    public function parameters(): array;
}
