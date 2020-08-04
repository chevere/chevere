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

namespace Chevere\Components\Parameter;

use Chevere\Components\DataStructures\Traits\DsMapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;

final class Parameters implements ParametersInterface
{
    use DsMapTrait;

    public function toArray(): array
    {
        return $this->map->toArray();
    }

    public function withAdded(ParameterInterface $parameter): ParametersInterface
    {
        if ($this->map->hasKey($parameter->name())) {
            throw new OverflowException(
                (new Message('Parameter %name% has been already added'))
                    ->code('%name%', $parameter->name())
            );
        }
        $new = clone $this;
        $new->map->put($parameter->name(), $parameter);

        return $new;
    }

    public function withModify(ParameterInterface $parameter): ParametersInterface
    {
        if (!$this->map->hasKey($parameter->name())) {
            throw new OutOfBoundsException(
                (new Message("Parameter %name% doesn't exists"))
                    ->code('%name%', $parameter->name())
            );
        }
        $new = clone $this;
        $new->map->put($parameter->name(), $parameter);

        return $new;
    }

    public function has(string $name): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $name);
    }

    public function get(string $name): ParameterInterface
    {
        try {
            /**
             * @var ParameterInterface $return
             */
            $return = $this->map->get($name);

            return $return;
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Name %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }
}
