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

use BadMethodCallException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\PathUriInterface;
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
        return isset($this->array[$pathUri->key()]);
    }

    public function get(PathUri $pathUri): array
    {
        $get = $this->array[$pathUri->key()] ?? null;
        if ($get === null) {
            throw new BadMethodCallException(
                (new Message("PathUri key %key% doesn't exists"))
                    ->code('%key%', $pathUri->key())
                    ->toString()
            );
        }

        return $get;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
