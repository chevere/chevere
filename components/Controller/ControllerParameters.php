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

namespace Chevere\Components\Controller;

use Chevere\Components\Controller\Interfaces\ControllerParameterInterface;
use Chevere\Components\DataStructures\Traits\DsMapTrait;
use OutOfBoundsException;

final class ControllerParameters
{
    use DsMapTrait;

    public function put(ControllerParameterInterface $controllerParameter): void
    {
        $this->map->put($controllerParameter->name(), $controllerParameter);
    }

    public function hasKey(string $name): bool
    {
        return $this->map->hasKey($name);
    }

    /**
     * @throws OutOfBoundsException if the parameter doesn't exists
     */
    public function get(string $name): ControllerParameterInterface
    {
        return $this->map->get($name);
    }
}
