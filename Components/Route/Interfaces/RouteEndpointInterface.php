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

namespace Chevere\Components\Route\Interfaces;

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Methods\ConnectMethod;
use Chevere\Components\Http\Methods\DeleteMethod;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Methods\HeadMethod;
use Chevere\Components\Http\Methods\OptionsMethod;
use Chevere\Components\Http\Methods\PatchMethod;
use Chevere\Components\Http\Methods\PostMethod;
use Chevere\Components\Http\Methods\PutMethod;
use Chevere\Components\Http\Methods\TraceMethod;

interface RouteEndpointInterface
{
    const KNOWN_METHODS = [
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

    public function method(): MethodInterface;

    public function controller(): ControllerInterface;

    public function withDescription(string $description): RouteEndpointInterface;

    public function description(): string;

    public function withoutParameter(string $parameter): RouteEndpointInterface;

    public function parameters(): array;
}
