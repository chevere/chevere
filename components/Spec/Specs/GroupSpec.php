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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\Spec\Specs\RoutableSpecs;
use Chevere\Components\Spec\Specs\Traits\SpecsTrait;
use Chevere\Interfaces\Spec\SpecInterface;
use Chevere\Interfaces\Spec\SpecPathInterface;

final class GroupSpec implements SpecInterface
{
    use SpecsTrait;

    private RoutableSpecs $routableSpecs;

    /**
     * @var SpecPathInterface $specRoot /spec
     */
    public function __construct(SpecPathInterface $specRoot, string $groupName)
    {
        $this->jsonPath = $specRoot->getChild("$groupName/routes.json")->pub();
        $this->key = $groupName;
        $this->routableSpecs = new RoutableSpecs;
    }

    public function withAddedRoutableSpec(RoutableSpec $routableSpec): GroupSpec
    {
        $new = clone $this;
        $new->routableSpecs->put($routableSpec);

        return $new;
    }

    public function toArray(): array
    {
        $routes = [];
        /**
         * @var string $key
         * @var RoutableSpec $routableSpec
         */
        foreach ($this->routableSpecs->mapCopy() as $key => $routableSpec) {
            $routes[$key] = $routableSpec->toArray();
        }

        return [
            'name' => $this->key,
            'spec' => $this->jsonPath,
            'routes' => $routes,
        ];
    }
}
