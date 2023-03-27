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

use Chevere\DataStructure\Map;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;

trait ParametersTrait
{
    /**
     * @var Map<TValue>
     */
    private Map $map;

    /**
     * @var array<string>
     */
    private array $required;

    /**
     * @var array<string>
     */
    private array $optional;

    public function required(): array
    {
        return $this->required;
    }

    public function optional(): array
    {
        return $this->optional;
    }

    public function isRequired(string ...$name): bool
    {
        foreach ($name as $item) {
            $this->assertNoOutOfRange($item);
            if (array_search($item, $this->required, true) === false) {
                return false;
            }
        }

        return true;
    }

    public function isOptional(string ...$name): bool
    {
        foreach ($name as $item) {
            $this->assertNoOutOfRange($item);
            if (array_search($item, $this->required, true) !== false) {
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

    public function withModified(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameter as $name => $param) {
            $name = strval($name);
            if (! $new->map->has($name)) {
                throw new OutOfBoundsException(
                    message("Parameter %name% doesn't exists")
                        ->withCode('%name%', $name)
                );
            }
            $new->map = $new->map
                ->withPut(...[
                    $name => $param,
                ]);
        }

        return $new;
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
            $this->{$property}[] = $name;
            $map[$name] = $parameter;
        }
        $this->map = $this->map->withPut(...$map);
    }

    private function removeProperty(string ...$name): void
    {
        $this->map = $this->map->withOut(...$name);
        $this->required = array_values(
            array_diff($this->required, $name)
        );
        $this->optional = array_values(
            array_diff($this->optional, $name)
        );
    }
}
