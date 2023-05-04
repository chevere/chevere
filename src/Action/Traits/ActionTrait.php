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

namespace Chevere\Action\Traits;

use Chevere\Attribute\StringAttribute;
use function Chevere\Message\message;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayp;
use Chevere\Parameter\ArrayParameter;
use function Chevere\Parameter\assertArgument;
use Chevere\Parameter\BooleanParameter;
use Chevere\Parameter\FloatParameter;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\ObjectParameter;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Errors\TypeError;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @method array<string, mixed> run()
 */
trait ActionTrait
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

    protected ArrayTypeParameterInterface $acceptResponse;

    protected ReflectionMethod $reflection;

    public static function description(): string
    {
        return '';
    }

    public function isStrict(): bool
    {
        return true;
    }

    public static function acceptResponse(): ArrayTypeParameterInterface
    {
        return arrayp();
    }

    final public function getResponse(mixed ...$argument): ResponseInterface
    {
        $arguments = arguments(self::parameters(), $argument)->toArray();
        $data = $this->run(...$arguments);
        if ($this->isStrict()) {
            /** @var array<string, mixed> $data */
            $data = assertArgument($this->acceptResponse, $data);
        }

        return new Response(...$data);
    }

    final protected static function getParameters(): ParametersInterface
    {
        $collection = [
            0 => [],
            1 => [],
        ];
        $reflection = new ReflectionMethod(static::class, 'run');
        foreach ($reflection->getParameters() as $reflectionParameter) {
            $attribute = static::getAttribute($reflectionParameter);
            $default = static::getDefaultValue($reflectionParameter);
            $namedType = $reflectionParameter->getType();
            if ($namedType === null) {
                throw new TypeError(
                    message: message('Missing type declaration for parameter %parameter%')
                        ->withTranslate('%parameter%', '$' . $reflectionParameter->getName())
                );
            }
            /** @var ReflectionNamedType $namedType */
            $typeName = $namedType->getName();
            $type = static::getTypeToParameter($reflectionParameter);
            $parameter = new $type($attribute->description());
            if ($parameter instanceof ObjectParameterInterface) {
                $parameter = $parameter->withClassName($typeName);
            }
            if ($default !== null && method_exists($parameter, 'withDefault')) {
                $parameter = $parameter->withDefault($default);
            }
            $parameter = static::getParameterWithSome($parameter, $attribute);
            $pos = intval(! $reflectionParameter->isOptional());
            $collection[$pos][$reflectionParameter->getName()] = $parameter;
        }

        return (new Parameters())
            ->withAddedRequired(...$collection[1])
            ->withAddedOptional(...$collection[0]);
    }

    final protected static function getAttribute(ReflectionParameter $parameter): StringAttribute
    {
        $reflectionAttributes = $parameter->getAttributes(StringAttribute::class);
        /**
         * @phpstan-ignore-next-line
         * @var ?ReflectionAttribute $reflectionAttribute
         */
        $reflectionAttribute = $reflectionAttributes[0] ?? null;
        if ($reflectionAttribute !== null) {
            /** @var StringAttribute */
            return $reflectionAttribute->newInstance();
        }

        return new StringAttribute();
    }

    final protected static function getDefaultValue(ReflectionParameter $reflection): mixed
    {
        return $reflection->isDefaultValueAvailable()
            ? $reflection->getDefaultValue()
            : null;
    }

    final protected static function getParameterWithSome(
        ParameterInterface $parameter,
        StringAttribute $attribute
    ): ParameterInterface {
        if (! ($parameter instanceof StringParameterInterface)) {
            return $parameter;
        }

        return $parameter->withRegex($attribute->regex());
    }

    final protected static function getTypeToParameter(ReflectionParameter $reflection): string
    {
        /** @var ReflectionNamedType $namedType */
        $namedType = $reflection->getType();
        $type = self::TYPE_TO_PARAMETER[$namedType->getName()] ?? null;
        if ($type === null) {
            $type = self::TYPE_TO_PARAMETER['object'];
        }

        return $type;
    }
}
