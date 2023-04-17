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

use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ArrayTypeParameterTrait;
use Chevere\Parameter\Traits\ParameterAssertArrayTypeTrait;
use Chevere\Parameter\Traits\ParametersAccessTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class ArrayParameter implements ArrayParameterInterface
{
    use ArrayParameterTrait;
    use ArrayTypeParameterTrait;
    use ParameterAssertArrayTypeTrait;
    use ParametersAccessTrait;
    use ParameterTrait;

    /**
     * @var array<mixed, mixed>
     */
    private ?array $default;

    public function setUp(): void
    {
        $this->parameters = new Parameters();
    }

    public function withRequired(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        $new->put('withAddedRequired', ...$parameter);

        return $new;
    }

    public function withOptional(ParameterInterface ...$parameter): static
    {
        $new = clone $this;
        $new->put('withAddedOptional', ...$parameter);

        return $new;
    }

    public function schema(): array
    {
        $items = [];
        foreach ($this->parameters as $name => $parameter) {
            $items[$name] = $parameter->schema();
            $items[$name]['isRequired'] = $this->parameters->isRequired($name);
        }

        return [
            'type' => $this->type->primitive(),
            'description' => $this->description,
            'default' => $this->default,
            'items' => $items,
        ];
    }

    public function assertCompatible(ArrayParameterInterface $parameter): void
    {
        $this->assertArrayType($parameter);
    }
}
