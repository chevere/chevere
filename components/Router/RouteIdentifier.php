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

use Chevere\Components\Router\Interfaces\RouteIdentifierInterface;

final class RouteIdentifier implements RouteIdentifierInterface
{
    private int $id;

    private string $group;

    private string $name;

    public function __construct(int $id, string $group, string $name)
    {
        $this->id = $id;
        $this->group = $group;
        $this->name = $name;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'name' => $this->name,
        ];
    }
}
