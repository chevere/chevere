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

use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ReflectionParameterTypedInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Throwable;
use function Chevere\Attribute\descriptionAttribute;
use function Chevere\Attribute\enumAttribute;
use function Chevere\Attribute\regexAttribute;
use function Chevere\Message\message;

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

    private ParameterInterface $parameter;

    public function __construct(
        private ReflectionParameter $reflection
    ) {
        $this->type = $this->getType();
        $description = descriptionAttribute($this->reflection);
        $default = $this->getDefaultValue();
        $type = $this->getParameterType();
        $parameter = new $type($description->__toString());
        if ($parameter instanceof ObjectParameterInterface) {
            $typeName = $this->type->getName();
            $parameter = $parameter->withClassName($typeName);
        }
        if ($default !== null && method_exists($parameter, 'withDefault')) {
            $parameter = $parameter->withDefault($default);
        }
        $this->parameter = $this->getParameterWithRegex(
            $parameter,
            $this->getRegex()
        );
    }

    public function parameter(): ParameterInterface
    {
        return $this->parameter;
    }

    private function getRegex(): RegexInterface
    {
        try {
            $attribute = enumAttribute($this->reflection);
        } catch (Throwable) {
            $attribute = regexAttribute($this->reflection);
        }

        return $attribute->regex();
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

    private function getDefaultValue(): mixed
    {
        return $this->reflection->isDefaultValueAvailable()
            ? $this->reflection->getDefaultValue()
            : null;
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

    private function getParameterWithRegex(
        ParameterInterface $parameter,
        RegexInterface $regex
    ): ParameterInterface {
        if (! ($parameter instanceof StringParameterInterface)) {
            return $parameter;
        }

        return $parameter->withRegex($regex);
    }
}
