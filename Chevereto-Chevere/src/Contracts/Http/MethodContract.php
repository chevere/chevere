<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Contracts\Http;

interface MethodContract
{
    public function __construct(string $method);

    public function method(): string;

    public function withController(string $controller): MethodContract;

    public function hasController(): bool;

    public function controller(): string;
}
