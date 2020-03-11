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

namespace Chevere\Components\Spec;

use Chevere\Components\Router\Interfaces\RouterInterface;

/**
 * The Chevere Spec
 *
 * A collection of application routes and its endpoints.
 */
final class Spec
{
    public function __construct(RouterInterface $router)
    {
    }
}
