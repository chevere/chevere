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
use Chevere\Interfaces\Controller\ControllerParameterInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use OutOfBoundsException;

final class ControllerParameters implements ControllerParametersInterface
{
    use DsMapTrait;

    public function withParameter(ControllerParameterInterface $controllerParameter): ControllerParametersInterface
    {
        $new = clone $this;
        $key = $controllerParameter->name();
        $value = $controllerParameter;
        $new->map->put($key, $value);

        return $new;
    }

    public function hasParameterName(string $name): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $name);
    }

    /**
     * @throws OutOfBoundsException if the parameter doesn't exists
     */
    public function get(string $name): ControllerParameterInterface
    {
        /**
         * @var ControllerParameterInterface $return
         */
        $return = $this->map->get($name);

        return $return;
    }
}
