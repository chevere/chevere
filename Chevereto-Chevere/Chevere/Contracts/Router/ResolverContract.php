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

namespace Chevere\Contracts\Router;

use Chevere\Contracts\Route\RouteContract;

interface ResolverContract
{
    public function __construct(string $serialized);

    public function route(): RouteContract;
}
