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

namespace Chevere\Parameter;

use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapTrait;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use Chevere\Message\Message;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Ds\Set;

final class Parameters implements ParametersInterface
{
    use MapTrait;

    private Set $required;

    private Set $optional;

    public function __construct(ParameterInterface ...$parameters)
    {
        $this->map = new Map();
        $this->required = new Set();
        $this->optional = new Set();
        $this->putAdded(...$parameters);
    }

    public function __clone()
    {
        $this->map = clone $this->map;
        $this->required = new Set($this->required->toArray());
        $this->optional = new Set($this->optional->toArray());
    }

    public function withAdded(ParameterInterface ...$parameters): ParametersInterface
    {
        $new = clone $this;
        $new->putAdded(...$parameters);

        return $new;
    }

    public function withAddedOptional(ParameterInterface ...$parameters): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameters as $name => $param) {
            $name = strval($name);
            $new->assertNoOverflow($name);
            $new->map = $new->map->withPut($name, $param);
            $new->optional->add($name);
        }
        
        return $new;
    }

    public function withModify(ParameterInterface ...$parameters): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameters as $name => $param) {
            $name = strval($name);
            if (!$new->map->has($name)) {
                throw new OutOfBoundsException(
                    (new Message("Parameter %name% doesn't exists"))
                        ->code('%name%', $name)
                );
            }
            $new->map = $new->map->withPut($name, $param);
        }

        return $new;
    }

    public function has(string ...$parameter): bool
    {
        return $this->map->has(...$parameter);
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

    public function required(): Set
    {
        return new Set($this->required->toArray());
    }

    public function optional(): Set
    {
        return new Set($this->optional->toArray());
    }

    private function putAdded(ParameterInterface ...$parameters): void
    {
        foreach ($parameters as $name => $parameter) {
            $name = strval($name);
            $this->assertNoOverflow($name);
            $this->map = $this->map->withPut($name, $parameter);
            $this->required->add($name);
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

    private function assertNoOverflow(string $name): void
    {
        if ($this->has($name)) {
            throw new OverflowException(
                (new Message('Parameter %name% has been already added'))
                    ->code('%name%', $name)
            );
        }
    }
}
