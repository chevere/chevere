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

namespace Chevere\Contracts\Http;

interface MethodContract
{
    /** Array containing all the known HTTP methods. */
    const ACCEPT_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'COPY', 'HEAD', 'OPTIONS', 'LINK', 'UNLINK', 'PURGE', 'LOCK', 'UNLOCK', 'PROPFIND', 'VIEW', 'TRACE', 'CONNECT'];

    public function __construct(string $method);

    public function method(): string;

    public function withControllerName(string $controller): MethodContract;

    public function hasControllerName(): bool;

    public function controllerName(): string;
}
