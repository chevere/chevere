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

use BadMethodCallException;
use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Map;
use Chevere\DataStructure\Traits\MapTrait;
use Chevere\DataStructure\Vector;
use Chevere\Parameter\Interfaces\ParameterCastInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use InvalidArgumentException;
use OverflowException;
use function Chevere\Message\message;

final class Parameters implements ParametersInterface
{
    /**
     * @template-use MapTrait<ParameterInterface>
     */
    use MapTrait;

    /**
     * @var Map<ParameterInterface>
     */
    private Map $map;

    /**
     * @var Vector<string>
     */
    private VectorInterface $requiredKeys;

    /**
     * @var Vector<string>
     */
    private VectorInterface $optionalKeys;

    private int $optionalMinimum = 0;

    /**
     * @param ParameterInterface $parameter Required parameters
     */
    public function __construct(ParameterInterface ...$parameter)
    {
        $this->map = new Map();
        $this->requiredKeys = new Vector();
        $this->optionalKeys = new Vector();
        foreach ($parameter as $name => $item) {
            $name = strval($name);
            $this->addProperty('requiredKeys', $name, $item);
        }
    }

    public function withRequired(string $name, ParameterInterface $parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('requiredKeys', $name, $parameter);

        return $new;
    }

    public function withOptional(string $name, ParameterInterface $parameter): ParametersInterface
    {
        $new = clone $this;
        $new->addProperty('optionalKeys', $name, $parameter);

        return $new;
    }

    public function withMakeOptional(string ...$name): ParametersInterface
    {
        $new = clone $this;
        if ($name === []) {
            $name = $this->requiredKeys->toArray();
        }
        foreach ($name as $key) {
            if (! $new->requiredKeys->contains($key)) {
                throw new InvalidArgumentException(
                    (string) message(
                        'Parameter `%name%` is not required',
                        name: $key
                    )
                );
            }
            $parameter = $new->get($key);
            $new->remove($key);
            $new->addProperty('optionalKeys', $key, $parameter);
        }

        return $new;
    }

    public function withMakeRequired(string ...$name): ParametersInterface
    {
        $new = clone $this;
        if ($name === []) {
            $name = $this->optionalKeys->toArray();
        }
        foreach ($name as $key) {
            if (! $new->optionalKeys()->contains($key)) {
                throw new InvalidArgumentException(
                    (string) message(
                        'Parameter `%name%` is not optional',
                        name: $key
                    )
                );
            }
            $parameter = $new->get($key);
            $new->remove($key);
            $new->addProperty('requiredKeys', $key, $parameter);
        }

        return $new;
    }

    public function without(string ...$name): ParametersInterface
    {
        $new = clone $this;
        $new->remove(...$name);

        return $new;
    }

    public function withOptionalMinimum(int $count): ParametersInterface
    {
        match (true) {
            $count < 0 => throw new InvalidArgumentException(
                (string) message('Count must be greater or equal to 0')
            ),
            $this->optionalKeys()->count() === 0 => throw new BadMethodCallException(
                (string) message('No optional parameters found')
            ),
            default => null,
        };
        $new = clone $this;
        $new->optionalMinimum = $count;
        $new->assertMinimumOptional();

        return $new;
    }

    public function requiredKeys(): VectorInterface
    {
        return $this->requiredKeys;
    }

    public function optionalKeys(): VectorInterface
    {
        return $this->optionalKeys;
    }

    public function optionalMinimum(): int
    {
        return $this->optionalMinimum;
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

    public function required(string $name): ParameterCastInterface
    {
        if ($this->optionalKeys()->contains($name)) {
            throw new InvalidArgumentException(
                (string) message(
                    'Parameter `%name%` is optional',
                    name: $name
                )
            );
        }

        return new ParameterCast(
            $this->get($name)
        );
    }

    public function optional(string $name): ParameterCastInterface
    {
        if (! $this->optionalKeys()->contains($name)) {
            throw new InvalidArgumentException(
                (string) message(
                    'Parameter `%name%` is required',
                    name: $name
                )
            );
        }

        return new ParameterCast(
            $this->get($name)
        );
    }

    private function remove(string ...$name): void
    {
        $this->map = $this->map->without(...$name);
        $requiredDiff = array_diff($this->requiredKeys->toArray(), $name);
        $optionalDiff = array_diff($this->optionalKeys->toArray(), $name);
        $this->requiredKeys = new Vector(...$requiredDiff);
        $this->optionalKeys = new Vector(...$optionalDiff);
        $this->assertMinimumOptional();
    }

    private function assertMinimumOptional(): void
    {
        if ($this->optionalMinimum > $this->optionalKeys()->count()) {
            throw new InvalidArgumentException(
                (string) message(
                    'Count must be less or equal to `%optional%`',
                    optional: strval($this->optionalMinimum)
                )
            );
        }
    }

    private function addProperty(string $property, string $name, ParameterInterface $parameter): void
    {
        if ($this->has($name)) {
            throw new OverflowException(
                (string) message(
                    'Parameter `%name%` has been already added',
                    name: $name
                )
            );
        }
        $this->{$property} = $this->{$property}->withPush($name);
        $this->map = $this->map->withPut($name, $parameter);
    }
}
