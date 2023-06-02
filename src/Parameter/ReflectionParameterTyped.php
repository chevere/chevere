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

use Chevere\Attribute\StringRegex;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ReflectionParameterTypedInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use ReflectionAttribute;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

final class ReflectionParameterTyped implements ReflectionParameterTypedInterface
{
    /**
     * @var array<string, string>
     */
    public const TYPE_TO_PARAMETER = [
        'array' => ArrayParameter::class,
        'bool' => BooleanParameter::class,
        'float' => FloatParameter::class,
        'int' => IntegerParameter::class,
        'string' => StringParameter::class,
        'object' => ObjectParameter::class,
    ];

    private ReflectionNamedType $type;

    private StringRegex $attribute;

    private mixed $default;

    private ParameterInterface $parameter;

    public function __construct(
        private ReflectionParameter $reflection
    ) {
        $this->type = $this->getType();
        $this->attribute = $this->getAttribute();
        $this->default = $this->getDefaultValue();
        $type = $this->getParameterType();
        $parameter = new $type($this->attribute->description());
        if ($parameter instanceof ObjectParameterInterface) {
            $typeName = $this->type->getName();
            $parameter = $parameter->withClassName($typeName);
        }
        if ($this->default !== null && method_exists($parameter, 'withDefault')) {
            $parameter = $parameter->withDefault($this->default);
        }
        $this->parameter = $this->getParameterWithAttribute($parameter, $this->attribute);
    }

    public function attribute(): StringRegex
    {
        return $this->attribute;
    }

    public function default(): mixed
    {
        return $this->default;
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
                message('Missing type declaration for parameter %parameter%')
                    ->withTranslate('%parameter%', '$' . $this->reflection->getName())
            );
        }
        if ($reflectionType instanceof ReflectionNamedType) {
            return $reflectionType;
        }
        $name = '$' . $this->reflection->getName();
        $type = $this->getReflectionType($reflectionType);

        throw new InvalidArgumentException(
            message('Parameter %name% of type %type% is not supported')
                ->withTranslate('%name%', $name)
                ->withTranslate('%type%', $type)
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

    private function getAttribute(): StringRegex
    {
        $attributes = $this->reflection->getAttributes(StringRegex::class);
        /**
         * @phpstan-ignore-next-line
         * @var ?ReflectionAttribute $attribute
         */
        $attribute = $attributes[0] ?? null;
        if ($attribute !== null) {
            /** @var StringRegex */
            return $attribute->newInstance();
        }

        return new StringRegex();
    }

    private function getDefaultValue(): mixed
    {
        return $this->reflection->isDefaultValueAvailable()
            ? $this->reflection->getDefaultValue()
            : null;
    }

    private function getParameterType(): string
    {
        $type = self::TYPE_TO_PARAMETER[$this->type->getName()] ?? null;
        if ($type === null) {
            return self::TYPE_TO_PARAMETER['object'];
        }

        return $type;
    }

    private function getParameterWithAttribute(
        ParameterInterface $parameter,
        StringRegex $attribute
    ): ParameterInterface {
        if (! ($parameter instanceof StringParameterInterface)) {
            return $parameter;
        }

        return $parameter->withRegex($attribute->regex());
    }
}
