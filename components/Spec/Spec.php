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

use Chevere\Components\Router\Interfaces\RouteableInterface;
use Chevere\Components\Spec\Interfaces\SpecCacheInterface;
use Chevere\Components\Spec\Interfaces\SpecInterface;

/**
 * A collection of application route groups, its routes and endpoints.
 */
final class Spec implements SpecInterface
{
    private SpecCacheInterface $specCache;

    private array $array = [];

    public function __construct()
    {
    }

    public function withRouteable(int $id, RouteableInterface $routeable): SpecInterface
    {
        $new = clone $this;
        $methodControllerNames = $routeable->route()->methodControllerNameCollection();
        $methods = [];
        foreach ($methodControllerNames->toArray() as $methodControllerName) {
            $methods[$methodControllerName->method()->name()] = '/spec/wea/.json';
        }
        $new->array[$id] = $methods;
    }
}
