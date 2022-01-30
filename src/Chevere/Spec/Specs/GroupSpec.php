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

namespace Chevere\Spec\Specs;

use Chevere\Filesystem\Interfaces\DirInterface;
use Chevere\Spec\Interfaces\Specs\GroupSpecInterface;
use Chevere\Spec\Interfaces\Specs\RoutableSpecsInterface;
use Chevere\Spec\Interfaces\Specs\RouteSpecInterface;
use Chevere\Spec\Specs\Traits\SpecsTrait;

final class GroupSpec implements GroupSpecInterface
{
    use SpecsTrait;

    private RoutableSpecsInterface $routableSpecs;

    public function __construct(DirInterface $specDir, string $groupName)
    {
        $this->jsonPath = $specDir
            ->getChild("${groupName}/")
            ->path()
            ->__toString() . 'routes.json';
        $this->key = $groupName;
        $this->routableSpecs = new RoutableSpecs();
    }

    public function withAddedRoutableSpec(RouteSpecInterface $routableSpec): GroupSpecInterface
    {
        $new = clone $this;
        $new->routableSpecs = $new->routableSpecs->withPut($routableSpec);

        return $new;
    }

    public function toArray(): array
    {
        $routes = [];
        foreach ($this->routableSpecs->getIterator() as $key => $routableSpec) {
            $routes[$key] = $routableSpec->toArray();
        }

        return [
            'name' => $this->key,
            'spec' => $this->jsonPath,
            'routes' => $routes,
        ];
    }
}
