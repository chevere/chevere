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

use Chevere\Components\Spec\Specs\Traits\SpecsTrait;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Spec\Specs\GroupSpecInterface;
use Chevere\Interfaces\Spec\Specs\GroupSpecsInterface;
use Chevere\Interfaces\Spec\Specs\IndexSpecInterface;

final class IndexSpec implements IndexSpecInterface
{
    use SpecsTrait;

    private GroupSpecsInterface $groupSpecs;

    public function __construct(DirInterface $specDir)
    {
        $this->jsonPath = $specDir->path()->toString() . 'index.json';
        $this->groupSpecs = new GroupSpecs();
    }

    public function withAddedGroup(GroupSpecInterface $groupSpec): IndexSpecInterface
    {
        $new = clone $this;
        $new->groupSpecs = $new->groupSpecs->withPut($groupSpec);

        return $new;
    }

    public function toArray(): array
    {
        $repositories = [];
        foreach ($this->groupSpecs->getIterator() as $key => $groupSpec) {
            $repositories[$key] = $groupSpec->toArray();
        }

        return [
            'repositories' => $repositories,
        ];
    }
}
