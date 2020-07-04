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

namespace Chevere\Interfaces\Spec;

use Chevere\Interfaces\DataStructures\DsMapInterface;
use Chevere\Interfaces\Spec\GroupSpecInterface;
use Generator;

interface GroupSpecsInterface extends DsMapInterface
{
    /**
     * @return Generator<string, GroupSpecInterface>
     */
    public function getGenerator(): Generator;

    public function put(GroupSpecInterface $groupSpec): void;

    public function hasKey(string $key): bool;

    public function get(string $key): GroupSpecInterface;
}
