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

use Chevere\Components\Router\Interfaces\RouterNamedInterface;

final class RouterNamed implements RouterNamedInterface
{
    private array $array = [];

    public function withAdded(string $name, int $id): RouterNamedInterface
    {
        $new = clone $this;
        $new->array[$name] = $id;

        return $new;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
