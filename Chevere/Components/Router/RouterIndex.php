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

    public function withAdded(string $path, int $id, string $group, string $name): RouterIndexInterface
    {
        $new = clone $this;
        $array = [
            'id' => $id,
            'group' => $group,
            'name' => $name,
        ];
        if (array_key_exists($path, $this->array)) {
            $new->array[$path] = [$array];
        } else {
            $new->array[$path][] = $array;
        }

        return $new;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
