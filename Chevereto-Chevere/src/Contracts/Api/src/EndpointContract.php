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

namespace Chevere\Contracts\Api\src;

use Chevere\Contracts\Http\MethodsContract;

interface EndpointContract
{
    public function __construct(MethodsContract $methods);

    public function methods(): MethodsContract;

    public function toArray(): array;

    public function setResource(array $resource): void;
}
