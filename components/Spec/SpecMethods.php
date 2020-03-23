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

use Chevere\Components\DataStructures\Traits\DsMapTrait;

final class SpecMethods
{
    use DsMapTrait;

    public function put(string $methodName, string $jsonPath): void
    {
        $this->map->put($methodName, $jsonPath);
    }

    public function hasKey(string $methodName): bool
    {
        return $this->map->hasKey($methodName);
    }

    public function get(string $methodName): string
    {
        return $this->map->get($methodName);
    }
}
