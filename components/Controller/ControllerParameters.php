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

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Controller\ControllerParameterInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;

final class ControllerParameters implements ControllerParametersInterface
{
    use DsMapTrait;

    public function toArray(): array
    {
        return $this->map->toArray();
    }

    public function withParameter(ControllerParameterInterface $controllerParameter): ControllerParametersInterface
    {
        $new = clone $this;
        $new->map->put(
            $controllerParameter->name(),
            $controllerParameter
        );

        return $new;
    }

    public function hasParameterName(string $name): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $name);
    }

    public function get(string $name): ControllerParameterInterface
    {
        try {
            /**
             * @var ControllerParameterInterface $return
             */
            $return = $this->map->get($name);

            return $return;
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException;
        }
    }
}
