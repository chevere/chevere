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

namespace Chevere\Spec\Interfaces\Specs;

use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Router\Interfaces\Route\RouteEndpointInterface;
use Chevere\Spec\Interfaces\SpecInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;

/**
 * Describes the component in charge of defining a route endpoint spec.
 */
interface RouteEndpointSpecInterface extends SpecInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(DirInterface $specDir, RouteEndpointInterface $routeEndpoint);
}
