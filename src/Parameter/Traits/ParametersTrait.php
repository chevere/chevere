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

namespace Chevere\Parameter\Traits;

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Map;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;

trait ParametersTrait
{
    /**
     * @var Map<TValue>
     */
    private Map $map;

    private VectorInterface $required;

    private VectorInterface $optional;

    public function required(): VectorInterface
    {
        return $this->required;
    }

    public function optional(): VectorInterface
    {
        return $this->optional;
    }

    public function isRequired(string ...$name): bool
    {
        foreach ($name as $item) {
            $this->assertNoOutOfRange($item);
            if (! $this->required->contains($item)) {
                return false;
            }
        }

        return true;
    }

    public function isOptional(string ...$name): bool
    {
        foreach ($name as $item) {
            $this->assertNoOutOfRange($item);
            if ($this->required->contains($item)) {
                return false;
            }
        }

        return true;
    }

    public function assertHas(string ...$name): void
    {
        $this->map->assertHas(...$name);
    }

    public function has(string ...$name): bool
    {
        return $this->map->has(...$name);
    }

    public function get(string $name): ParameterInterface
    {
        return $this->map->get($name);
    }

    private function assertNoOutOfRange(string $parameter): void
    {
        if (! $this->has($parameter)) {
            throw new OutOfBoundsException(
                message("Parameter %name% doesn't exists")
                    ->withCode('%name%', $parameter)
            );
        }
    }

    private function assertNoOverflow(string $name): void
    {
        if ($this->has($name)) {
            throw new OverflowException(
                message('Parameter %name% has been already added')
                    ->withCode('%name%', $name)
            );
        }
    }

    /**
     * @param array<string|int, ParameterInterface> $parameters
     */
    private function addProperty(string $property, array $parameters): void
    {
        if (count($parameters) === 0) {
            return;
        }
        $map = [];
        foreach ($parameters as $name => $parameter) {
            $name = strval($name);
            $this->assertNoOverflow($name);
            $this->{$property} = $this->{$property}->withPush($name);
            $map[$name] = $parameter;
        }
        $this->map = $this->map->withPut(...$map);
    }
}
