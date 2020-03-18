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

use Chevere\Components\Http\Interfaces\MethodInterface;
use Ds\Map;
use function DeepCopy\deep_copy;

final class SpecMethods
{
    private Map $map;

    public function __construct()
    {
        $this->map = new Map;
    }

    public function withMethodJsonSpecPath(MethodInterface $method, string $jsonPath): SpecMethods
    {
        $new = clone $this;
        $new->map = deep_copy($this->map);
        $new->map->put($method::name(), $jsonPath);

        return $new;
    }

    public function hasKey(MethodInterface $method): bool
    {
        return $this->map->hasKey($method::name());
    }

    public function get(MethodInterface $method): string
    {
        return $this->map->get($method::name());
    }
}
