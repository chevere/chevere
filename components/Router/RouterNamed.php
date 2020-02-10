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
use Chevere\Components\Str\StrAssert;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterNamedInterface;

final class RouterNamed implements RouterNamedInterface
{
    private array $array = [];

    public function withAdded(string $name, int $id): RouterNamedInterface
    {
        (new StrAssert($name))->notEmpty()->notCtypeSpace();
        $new = clone $this;
        $new->array[$name] = $id;

        return $new;
    }

    public function has(string $name): bool
    {
        return isset($this->array[$name]);
    }

    public function get(string $name): int
    {
        (new StrAssert($name))->notEmpty()->notCtypeSpace();
        $get = $this->array[$name] ?? null;
        if ($get === null) {
            throw new BadMethodCallException(
                (new Message("Name %name% doesn't exists"))
                    ->code('%name%', $name)
                    ->toString()
            );
        }

        return $this->array[$name];
    }

    public function getForId(int $id): string
    {
        $search = array_search($id, $this->array);
        if ($search === false) {
            throw new BadMethodCallException(
                (new Message("Id %id% doesn't exists"))
                    ->code('%id%', (string) $id)
                    ->toString()
            );
        }

        return $search;
    }

    public function toArray(): array
    {
        return $this->array;
    }
}
