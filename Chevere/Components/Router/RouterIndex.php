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

namespace Chevere\Components\Router;

use Chevere\Components\Router\Interfaces\RouterIndexInterface;

final class RouterIndex implements RouterIndexInterface
{
    private array $array = [];

    public function withAdded(string $key, int $id, string $group, string $name): RouterIndexInterface
    {
        $new = clone $this;
        $array = [
            'id' => $id,
            'group' => $group,
            'name' => $name,
        ];
        if (array_key_exists($key, $this->array)) {
            $new->array[$key] = [$array];
        } else {
            $new->array[$key][] = $array;
        }

        return $new;
    }

    public function has(string $path): bool
    {
        return array_key_exists($path, $this->array);
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
