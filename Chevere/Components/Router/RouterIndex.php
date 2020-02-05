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

use Chevere\Components\Route\Interfaces\PathUriInterface;
use Chevere\Components\Route\Interfaces\RouteNameInterface;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Router\Interfaces\RouterIndexInterface;

final class RouterIndex implements RouterIndexInterface
{
    private array $array = [];

    public function withAdded(PathUriInterface $pathUri, int $id, string $group, string $name): RouterIndexInterface
    {
        $new = clone $this;
        $new->array[$pathUri->key()] = [
            'id' => $id,
            'group' => $group,
            'name' => $name,
        ];

        return $new;
    }

    public function has(PathUri $pathUri): bool
    {
        return array_key_exists($pathUri->key(), $this->array);
    }

    public function get(PathUri $pathUri): array
    {
        return $this->array[$pathUri->key()];
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
