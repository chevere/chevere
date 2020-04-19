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
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\DataStructures\Traits\DsMapTrait;
use OutOfBoundsException;
use function DeepCopy\deep_copy;

final class Parameters implements ControllerParametersInterface
{
    use DsMapTrait;

    public function withParameter(ControllerParameterInterface $controllerParameter): ControllerParametersInterface
    {
        $new = clone $this;
        $new->map = deep_copy($new->map);
        /**
         * @var \Ds\TKey $key
         * @var \Ds\TValue $value
         */
        $key = $controllerParameter->name();
        $value = $controllerParameter;
        $new->map->put($key, $value);

        return $new;
    }

    public function hasKey(string $name): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $name);
    }

    /**
     * @throws OutOfBoundsException if the parameter doesn't exists
     */
    public function get(string $name): ControllerParameterInterface
    {
        /**
         * @var \Ds\TKey $name
         * @var ControllerParameterInterface $return
         */
        $return = $this->map->get($name);

        return $return;
    }
}
