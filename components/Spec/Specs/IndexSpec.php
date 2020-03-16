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

use Chevere\Components\Common\Interfaces\ToArrayInterface;
use Chevere\Components\Spec\Specs\GroupSpecObjectsRead;
use SplObjectStorage;

final class IndexSpec implements ToArrayInterface
{
    private SplObjectStorage $objects;

    private string $jsonPath;

    private $array = [
        'groups' => [],
    ];

    public function __construct(string $specPath)
    {
        $this->jsonPath = $specPath . 'index.json';
        $this->objects = new SplObjectStorage;
    }

    public function withAddedGroup(GroupSpec $groupSpec): IndexSpec
    {
        $new = clone $this;
        $this->objects->attach($groupSpec);
        $new->array['groups'][] = $groupSpec->toArray();

        return $new;
    }

    public function jsonPath(): string
    {
        return $this->jsonPath;
    }

    public function toArray(): array
    {
        return $this->array;
    }

    public function objects(): GroupSpecObjectsRead
    {
        return new GroupSpecObjectsRead($this->objects);
    }
}
