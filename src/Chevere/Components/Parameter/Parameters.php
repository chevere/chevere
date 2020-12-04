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

use Chevere\Components\DataStructures\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Ds\Map;
use Ds\Set;
use function DeepCopy\deep_copy;

final class Parameters implements ParametersInterface
{
    use MapTrait;

    private Set $required;

    public function __construct()
    {
        $this->map = new Map;
        $this->required = new Set;
    }

    public function __clone()
    {
        $this->map = deep_copy($this->map);
        $this->required = new Set($this->required->toArray());
    }

    public function withAddedRequired(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameter as $param) {
            $new->assertNoOverflow($param);
            $new->map->put($param->name(), $param);
            $new->required->add($param->name());
        }

        return $new;
    }

    public function withAddedOptional(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameter as $param) {
            $new->assertNoOverflow($param);
            $new->map->put($param->name(), $param);
        }

        return $new;
    }

    public function withModify(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameter as $param) {
            if (!$new->map->hasKey($param->name())) {
                throw new OutOfBoundsException(
                    (new Message("Parameter %name% doesn't exists"))
                        ->code('%name%', $param->name())
                );
            }
        }

        $new->map->put($param->name(), $param);

        return $new;
    }

    public function has(string $parameter): bool
    {
        return $this->map->hasKey(/** @scrutinizer ignore-type */ $parameter);
    }

    public function isRequired(string $parameter): bool
    {
        $this->assertNoOutOfBounds($parameter);

        return $this->required->contains($parameter);
    }

    public function isOptional(string $parameter): bool
    {
        $this->assertNoOutOfBounds($parameter);

        return !$this->required->contains($parameter);
    }

    public function get(string $name): ParameterInterface
    {
        try {
            return $this->map->get($name);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                (new Message('Parameter %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }

    private function assertNoOutOfBounds(string $parameter): void
    {
        if (!$this->has($parameter)) {
            throw new OutOfBoundsException(
                (new Message("Parameter %name% doesn't exists"))
                    ->code('%name%', $parameter)
            );
        }
    }

    private function assertNoOverflow(ParameterInterface $parameter): void
    {
        if ($this->has($parameter->name())) {
            throw new OverflowException(
                (new Message('Parameter %name% has been already added'))
                    ->code('%name%', $parameter->name())
            );
        }
    }
}
