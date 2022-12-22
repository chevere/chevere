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

use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;

final class ArrayParameter implements ArrayParameterInterface
{
    use ParameterTrait;

    /**
     * @var array<mixed, mixed>
     */
    private array $default = [];

    private ParametersInterface $parameters;

    public function setUp(): void
    {
        $this->parameters = new Parameters();
    }

    public function withDefault(array $value): ArrayParameterInterface
    {
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function withParameter(ParameterInterface ...$parameter): ArrayParameterInterface
    {
        $new = clone $this;
        $new->parameters = $new->parameters
            ->withAddedRequired(...$parameter);

        return $new;
    }

    public function default(): array
    {
        return $this->default;
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    /**
     * @throws TypeError
     * @throws OutOfBoundsException
     */
    public function assertCompatible(ArrayParameterInterface $parameter): void
    {
        foreach ($this->parameters as $name => $item) {
            try {
                $tryParameter = $parameter->parameters()->get($name);
            } catch (OutOfBoundsException) {
                throw new OutOfBoundsException(
                    message('Parameter %name% not found')
                        ->withCode('%name%', $name)
                );
            }

            try {
                $item->assertCompatible($tryParameter);
            } catch (\TypeError) {
                throw new InvalidArgumentException(
                    message('Parameter %name% of type %type% is not compatible with type %provided%')
                        ->withCode('%name%', $name)
                        ->withCode('%type%', $item::class)
                        ->withCode('%provided%', $tryParameter::class)
                );
            }
        }
    }

    private function getType(): TypeInterface
    {
        return new Type(Type::ARRAY);
    }
}
