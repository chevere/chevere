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
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;

final class Parameters implements ParametersInterface
{
    use MapTrait;

    /**
     * @var array<string>
     */
    private array $required;

    /**
     * @var array<string>
     */
    private array $optional;

    public function __construct(ParameterInterface ...$parameter)
    {
        $this->map = new Map();
        $this->required = [];
        $this->optional = [];
        $this->putAdded(...$parameter);
    }

    public function withAdded(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        $new->putAdded(...$parameter);

        return $new;
    }

    public function withAddedOptional(ParameterInterface ...$parameter): ParametersInterface
    {
        $new = clone $this;
        foreach ($parameter as $name => $param) {
            $name = strval($name);
            $new->assertNoOverflow($name);
            $new->map = $new->map->withPut($name, $param);
            $new->optional[] = $name;
        }

        return $new;
    }

    public function withModify(ParameterInterface ...$parameter): ParametersInterface
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
            $new->map = $new->map->withPut($name, $param);
        }

        return $new;
    }

    public function assertHas(string ...$name): void
    {
        $this->map->assertHas(...$name);
    }

    public function has(string ...$name): bool
    {
        return $this->map->has(...$name);
    }

    public function isRequired(string ...$name): bool
    {
        foreach ($name as $item) {
            $this->assertNoOutOfBounds($item);
            if (array_search($item, $this->required, true) === false) {
                return false;
            }
        }

        return true;
    }

    public function isOptional(string ...$name): bool
    {
        foreach ($name as $item) {
            $this->assertNoOutOfBounds($item);
            if (array_search($item, $this->required, true) !== false) {
                return false;
            }
        }

        return true;
    }

    public function get(string $name): ParameterInterface
    {
        try {
            /** @var ParameterInterface */
            return $this->map->get($name);
        } catch (\OutOfBoundsException $e) {
            throw new OutOfBoundsException(
                message('Parameter %name% not found')
                    ->withCode('%name%', $name)
            );
        }
    }

    public function required(): array
    {
        return $this->required;
    }

    public function optional(): array
    {
        return $this->optional;
    }

    private function putAdded(ParameterInterface ...$parameters): void
    {
        foreach ($parameters as $name => $parameter) {
            $name = strval($name);
            $this->assertNoOverflow($name);
            $this->map = $this->map->withPut($name, $parameter);
            $this->required[] = $name;
        }
    }

    private function assertNoOutOfBounds(string $parameter): void
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
}
