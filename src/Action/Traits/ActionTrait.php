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
use Chevere\Container\Container;
use function Chevere\Message\message;
use function Chevere\Parameter\arguments;
use function Chevere\Parameter\arrayParameter;
use Chevere\Parameter\ArrayParameter;
use function Chevere\Parameter\assertParameter;
use Chevere\Parameter\BooleanParameter;
use Chevere\Parameter\FloatParameter;
use Chevere\Parameter\IntegerParameter;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\ObjectParameter;
use function Chevere\Parameter\parameters;
use Chevere\Parameter\Parameters;
use Chevere\Parameter\StringParameter;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @method array<string, mixed> run(mixed ...$argument)
 */
trait ActionTrait
{
    /**
     * @var array<string, class-string<ParameterInterface>>
     */
    protected array $typeToParameter = [
        'array' => ArrayParameter::class,
        'bool' => BooleanParameter::class,
        'float' => FloatParameter::class,
        'int' => IntegerParameter::class,
        'string' => StringParameter::class,
        'object' => ObjectParameter::class,
    ];

    protected ParametersInterface $parameters;

    protected ArrayTypeParameterInterface $responseParameter;

    protected ParametersInterface $containerParameters;

    protected ContainerInterface $container;

    protected ReflectionMethod $reflection;

    public function isStrict(): bool
    {
        return true;
    }

    public function getContainerParameters(): ParametersInterface
    {
        return parameters();
    }

    public function getResponseParameter(): ArrayTypeParameterInterface
    {
        return arrayParameter();
    }

    final public function withContainer(ContainerInterface $container): static
    {
        $new = clone $this;
        $new->container = $container;
        $new->assertContainer();

        return $new;
    }

    final public function container(): ContainerInterface
    {
        return $this->container ??= new Container();
    }

    final public function getResponse(mixed ...$argument): ResponseInterface
    {
        $this->assertContainer();
        $arguments = arguments($this->parameters(), $argument)->toArray();
        $data = $this->run(...$arguments);
        if ($this->isStrict()) {
            assertParameter($this->responseParameter(), $data);
        }

        return new Response(...$data);
    }

    // @infection-ignore-all
    final public function containerParameters(): ParametersInterface
    {
        return $this->containerParameters ??= $this->getContainerParameters();
    }

    // @infection-ignore-all
    final public function parameters(): ParametersInterface
    {
        return $this->parameters ??= $this->getParameters();
    }

    // @infection-ignore-all
    final public function responseParameter(): ArrayTypeParameterInterface
    {
        return $this->responseParameter ??= $this->getResponseParameter();
    }

    final protected function assertContainer(): void
    {
        $missing = [];
        foreach ($this->containerParameters() as $name => $parameter) {
            if (! $this->container()->has($name)) {
                $className = $parameter::class;
                $missing[] = <<<STRING
                {$className} {$name}
                STRING;
            }
        }
        if ($missing !== []) {
            throw new InvalidArgumentException(
                message('Container for %action% does not provide parameter(s): %missing%')
                    ->withTranslate('%action%', $this::class)
                    ->withTranslate('%missing%', implode(', ', $missing))
            );
        }
    }

    final protected function getParameters(): ParametersInterface
    {
        $collection = [
            0 => [],
            1 => [],
        ];
        foreach ($this->reflection->getParameters() as $reflectionParameter) {
            $attribute = $this->getAttribute($reflectionParameter);
            $default = $this->getDefaultValue($reflectionParameter);
            $namedType = $reflectionParameter->getType();
            if ($namedType === null) {
                throw new TypeError(
                    message: message('Missing type declaration for parameter %parameter%')
                        ->withTranslate('%parameter%', '$' . $reflectionParameter->getName())
                );
            }
            /** @var ReflectionNamedType $namedType */
            $typeName = $namedType->getName();
            $type = $this->getTypeToParameter($reflectionParameter);
            $parameter = new $type($attribute->description());
            if ($parameter instanceof ObjectParameterInterface) {
                $parameter = $parameter->withClassName($typeName);
            }
            if ($default !== null && method_exists($parameter, 'withDefault')) {
                $parameter = $parameter->withDefault($default);
            }
            $parameter = $this->getParameterWithSome($parameter, $attribute);
            $pos = intval(! $reflectionParameter->isOptional());
            $collection[$pos][$reflectionParameter->getName()] = $parameter;
        }

        return (new Parameters())
            ->withAddedRequired(...$collection[1])
            ->withAddedOptional(...$collection[0]);
    }

    final protected function getAttribute(ReflectionParameter $parameter): StringAttribute
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

    final protected function getDefaultValue(ReflectionParameter $reflection): mixed
    {
        return $reflection->isDefaultValueAvailable()
            ? $reflection->getDefaultValue()
            : null;
    }

    final protected function getParameterWithSome(
        ParameterInterface $parameter,
        StringAttribute $attribute
    ): ParameterInterface {
        if (! ($parameter instanceof StringParameterInterface)) {
            return $parameter;
        }

        return $parameter->withRegex($attribute->regex());
    }

    final protected function getTypeToParameter(ReflectionParameter $reflection): string
    {
        /** @var ReflectionNamedType $namedType */
        $namedType = $reflection->getType();
        $type = $this->typeToParameter[$namedType->getName()] ?? null;
        if ($type === null) {
            $type = $this->typeToParameter['object'];
        }

        return $type;
    }
}
