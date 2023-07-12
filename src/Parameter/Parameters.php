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

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapTrait;
use Chevere\DataStructure\Vector;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ParametersGetTypedTrait;
use Chevere\Throwable\Exceptions\BadMethodCallException;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use function Chevere\Message\message;

final class Parameters implements ParametersInterface
{
    /**
     * @template-use MapTrait<ParameterInterface>
     */
    use MapTrait;

    use ParametersGetTypedTrait;

    /**
     * @var Map<ParameterInterface>
     */
    private Map $map;

    private VectorInterface $required;

    private VectorInterface $optional;

    private int $minimumOptional = 0;

    /**
     * @param ParameterInterface $parameter Required parameters
     */
    public function __construct(ParameterInterface ...$parameter)
    {
        $this->map = new Map();
        $this->required = new Vector();
        $this->optional = new Vector();
        foreach ($parameter as $name => $item) {
            $name = strval($name);
            $this->addProperty('required', $name, $item);
        }
    }

    public function withRequired(string $name, ParameterInterface $parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('required', $name, $parameter);

        return $new;
    }

    public function withOptional(string $name, ParameterInterface $parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('optional', $name, $parameter);

        return $new;
    }

    public function without(string ...$name): ParametersInterface
    {
        $new = clone $this;
        $new->map = $new->map->without(...$name);
        $requiredDiff = array_diff($new->required->toArray(), $name);
        $optionalDiff = array_diff($new->optional->toArray(), $name);
        $new->required = new Vector(...$requiredDiff);
        $new->optional = new Vector(...$optionalDiff);
        $new->assertMinimumOptional();

        return $new;
    }

    public function withMinimumOptional(int $count): ParametersInterface
    {
        match (true) {
            $count < 0 => throw new InvalidArgumentException(
                message('Count must be greater or equal to 0')
            ),
            $this->optional()->count() === 0 => throw new BadMethodCallException(
                message('No optional parameters found')
            ),
            default => null,
        };
        $new = clone $this;
        $new->minimumOptional = $count;
        $new->assertMinimumOptional();

        return $new;
    }

    public function required(): VectorInterface
    {
        return $this->required;
    }

    public function optional(): VectorInterface
    {
        return $this->optional;
    }

    public function minimumOptional(): int
    {
        return $this->minimumOptional;
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

    private function assertMinimumOptional(): void
    {
        if ($this->minimumOptional > $this->optional()->count()) {
            throw new InvalidArgumentException(
                message('Count must be less or equal to %optional%')
                    ->withCode('%optional%', strval($this->minimumOptional))
            );
        }
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

    private function addProperty(string $property, string $name, ParameterInterface $parameter): void
    {
        $this->assertNoOverflow($name);
        $this->{$property} = $this->{$property}->withPush($name);
        $this->map = $this->map->withPut($name, $parameter);
    }
}
