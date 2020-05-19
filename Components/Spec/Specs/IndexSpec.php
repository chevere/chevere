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

use Chevere\Components\Spec\Interfaces\SpecInterface;
use Chevere\Components\Spec\Interfaces\SpecPathInterface;
use Chevere\Components\Spec\Specs\GroupSpecs;
use Chevere\Components\Spec\Specs\Traits\SpecsTrait;

final class IndexSpec implements SpecInterface
{
    use SpecsTrait;

    private GroupSpecs $groupSpecs;

    public function __construct(SpecPathInterface $specPath)
    {
        $this->jsonPath = $specPath->getChild('index.json')->pub();
        $this->groupSpecs = new GroupSpecs;
    }

    public function withAddedGroup(GroupSpec $groupSpec): IndexSpec
    {
        $new = clone $this;
        $new->groupSpecs->put($groupSpec);

        return $new;
    }

    public function toArray(): array
    {
        $groups = [];
        /**
         * @var string $key
         * @var GroupSpec $groupSpec
         */
        foreach ($this->groupSpecs->map() as $key => $groupSpec) {
            $groups[$key] = $groupSpec->toArray();
        }

        return ['groups' => $groups];
    }
}
