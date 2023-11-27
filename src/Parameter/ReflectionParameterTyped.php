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

use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ReflectionParameterTypedInterface;
use InvalidArgumentException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Throwable;
use TypeError;
use function Chevere\Message\message;

final class ReflectionParameterTyped implements ReflectionParameterTypedInterface
{
    /**
     * @var array<string, string>
     */
    public const TYPE_TO_PARAMETER = [
        'array' => ArrayParameter::class,
        'bool' => BoolParameter::class,
        'float' => FloatParameter::class,
        'int' => IntParameter::class,
        'string' => StringParameter::class,
        'object' => ObjectParameter::class,
    ];

    private ReflectionNamedType $type;

    private ParameterInterface $parameter;

    public function __construct(
        private ReflectionParameter $reflection
    ) {
        $this->type = $this->getType();
        $type = $this->getParameterType();
        // @phpstan-ignore-next-line
        $this->parameter = new $type();

        try {
            $attribute = reflectedParameterAttribute($reflection);
            $this->parameter = $attribute->parameter();
        } catch (Throwable) {
        }
        if ($this->reflection->isDefaultValueAvailable() && method_exists($this->parameter, 'withDefault')) {
            $this->parameter = $this->parameter
                ->withDefault(
                    $this->reflection->getDefaultValue()
                );
        }
    }

    public function parameter(): ParameterInterface
    {
        return $this->parameter;
    }

    private function getType(): ReflectionNamedType
    {
        $reflectionType = $this->reflection->getType();
        if ($reflectionType === null) {
            throw new TypeError(
                (string) message(
                    'Missing type declaration for parameter %parameter%',
                    parameter: '$' . $this->reflection->getName()
                )
            );
        }
        if ($reflectionType instanceof ReflectionNamedType) {
            return $reflectionType;
        }
        $name = '$' . $this->reflection->getName();
        $type = $this->getReflectionType($reflectionType);

        throw new InvalidArgumentException(
            (string) message(
                'Parameter %name% of type %type% is not supported',
                name: $name,
                type: $type
            )
        );
    }

    /**
     * @infection-ignore-all
     */
    private function getReflectionType(mixed $reflectionType): string
    {
        return match (true) {
            $reflectionType instanceof ReflectionUnionType => 'union',
            $reflectionType instanceof ReflectionIntersectionType => 'intersection',
            default => 'unknown',
        };
    }

    private function getParameterType(): string
    {
        $type = self::TYPE_TO_PARAMETER[$this->type->getName()]
            ?? null;
        if ($type === null) {
            return self::TYPE_TO_PARAMETER['object'];
        }

        return $type;
    }
}
