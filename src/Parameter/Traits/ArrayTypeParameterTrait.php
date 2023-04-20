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

namespace Chevere\Parameter\Traits;

use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;

trait ArrayTypeParameterTrait
{
    private ParametersInterface $items;

    public function withDefault(array $value): static
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function without(string ...$name): static
    {
        $new = clone $this;
        $new->items = $new->items
            ->without(...$name);

        return $new;
    }

    private function put(string $method, ParameterInterface ...$parameter): void
    {
        $this->removeConflictKeys(...$parameter);
        $this->items = $this->items->{$method}(...$parameter);
    }

    private function removeConflictKeys(ParameterInterface ...$parameter): void
    {
        $keys = array_keys($parameter);
        /** @var string[] $diff */
        $diff = array_intersect($keys, $this->items->keys());
        $this->items = $this->items->without(...$diff);
    }
}
