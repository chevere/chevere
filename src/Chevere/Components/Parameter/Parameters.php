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

use Chevere\Components\DataStructure\Traits\MapTrait;
use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use function DeepCopy\deep_copy;
use Ds\Map;
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
        $this->map = deep_copy($this->map);
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
            $name = (string) $name;
            $new->assertNoOverflow($name);
            $new->map->put($name, $param);
            $new->optional->add($name);
        }

        return $new;
    }

    public function withModify(ParameterInterface ...$parameters): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameters as $name => $param) {
            $name = (string) $name;
            if (!$new->map->hasKey($name)) {
                throw new OutOfBoundsException(
                    (new Message("Parameter %name% doesn't exists"))
                        ->code('%name%', $name)
                );
            }
            $new->map->put($name, $param);
        }

        return $new;
    }

    public function has(string ...$parameter): bool
    {
        foreach ($parameter as $pos => $value) {
            if (!$this->map->hasKey($value)) {
                return false;
            }
        }

        return true;
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
            $name = (string) $name;
            $this->assertNoOverflow($name);
            $this->map->put($name, $parameter);
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
