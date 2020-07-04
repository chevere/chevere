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

use Chevere\Interfaces\DataStructures\DsMapInterface;
use Generator;

interface RoutableSpecsInterface extends DsMapInterface
{
    /**
     * @return Generator<string, RoutableSpecInterface>
     */
    public function getGenerator(): Generator;

    public function put(RoutableSpecInterface $routableSpec): void;

    public function hasKey(string $key): bool;

    public function get(string $key): RoutableSpecInterface;
}
