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

namespace Chevere\Interfaces\Spec\Specs;

use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Spec\SpecDirInterface;
use Chevere\Interfaces\Spec\SpecInterface;

/**
 * Describes the component in charge of defining a route endpoint spec.
 */
interface RouteEndpointSpecInterface extends SpecInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(SpecDirInterface $specPath, RouteEndpointInterface $routeEndpoint);
}
