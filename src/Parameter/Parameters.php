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

    public function __construct(ParameterInterface ...$parameters)
    {
        $this->map = new Map();
        $this->required = [];
        $this->optional = [];
        $this->putAdded(...$parameters);
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
            $new->optional[] = $name;
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
                    message("Parameter %name% doesn't exists")
                        ->withCode('%name%', $name)
                );
            }
            $new->map = $new->map->withPut($name, $param);
        }

        return $new;
    }

    public function assertHas(string ...$parameter): void
    {
        $this->map->assertHas(...$parameter);
    }

    public function has(string ...$parameter): bool
    {
        return $this->map->has(...$parameter);
    }

    public function isRequired(string $parameter): bool
    {
        $this->assertNoOutOfBounds($parameter);

        return array_search($parameter, $this->required)
            !== false;
    }

    public function isOptional(string $parameter): bool
    {
        $this->assertNoOutOfBounds($parameter);

        return array_search($parameter, $this->required)
            === false;
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
        if (!$this->has($parameter)) {
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
