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

use Chevere\Attributes\Description;
use Chevere\Attributes\Regex;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ReflectionParameterTypedInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use function Chevere\Attribute\getAttribute;
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

    private mixed $default;

    private ParameterInterface $parameter;

    public function __construct(
        private ReflectionParameter $reflection
    ) {
        $this->type = $this->getType();
        /** @var Regex $stringRegex */
        $stringRegex = getAttribute($this->reflection, Regex::class);
        /** @var Description $description */
        $description = getAttribute($this->reflection, Description::class);
        $this->default = $this->getDefaultValue();
        $type = $this->getParameterType();
        $parameter = new $type($description->__toString());
        if ($parameter instanceof ObjectParameterInterface) {
            $typeName = $this->type->getName();
            $parameter = $parameter->withClassName($typeName);
        }
        if ($this->default !== null && method_exists($parameter, 'withDefault')) {
            $parameter = $parameter->withDefault($this->default);
        }
        $this->parameter = $this->getParameterWithRegex($parameter, $stringRegex);
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

    private function getParameterWithRegex(
        ParameterInterface $parameter,
        Regex $attribute
    ): ParameterInterface {
        if (! ($parameter instanceof StringParameterInterface)) {
            return $parameter;
        }

        return $parameter->withRegex($attribute->regex());
    }
}
