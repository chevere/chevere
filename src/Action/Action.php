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

namespace Chevere\Action;

use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;
use Chevere\Common\Traits\DescriptionTrait;
use function Chevere\Message\message;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Attributes\ParameterAttribute;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Parameters;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

abstract class Action implements ActionInterface
{
    use DescriptionTrait;
    use ActionTrait;

    protected string $description;

    protected ParametersInterface $parameters;

    protected ParametersInterface $responseParameters;

    protected ParametersInterface $containerParameters;

    protected ContainerInterface $container;

    final public function __construct()
    {
        $this->setUpBefore();
        $this->parameters = $this->parameters();
        $this->containerParameters = $this->containerParameters();
        $this->assertRunParameters();
        $this->setUpAfter();
    }

    final public function getResponse(mixed ...$namedArguments): ResponseInterface
    {
        $this->assertContainer();
        $arguments = $this->getArguments(...$namedArguments)->toArray();
        $data = $this->run(...$arguments);
        if (!is_array($data)) {
            throw new TypeError(
                message('Method %method% must return an array.')
                    ->withStrtr('%method%', $this::class . '::run')
            );
        }

        return $this->getTypedResponse(...$data);
    }

    final protected function getTypedResponse(mixed ...$namedArguments): ResponseInterface
    {
        $arguments = new Arguments($this->responseParameters(), ...$namedArguments);

        return new Response(...$arguments->toArray());
    }

    final protected function assertContainer(): void
    {
        $missingService = [];
        $services = [];
        $keys = array_keys(
            iterator_to_array($this->containerParameters()->getIterator())
        );
        foreach ($keys as $name) {
            if (!$this->container()->has($name)) {
                $missingService[] = $name;

                continue;
            }
            $services[$name] = $this->container()->get($name);
        }
        if ($missingService !== []) {
            throw new InvalidArgumentException(
                message('The container for %action% does not provide the parameter(s): [%missing%]')
                    ->withStrtr('%action%', $this::class)
                    ->withStrtr('%missing%', implode(', ', $missingService))
            );
        }
    }

    final protected function getParameters(): ParametersInterface
    {
        $this->assertRunMethod();
        $reflection = new ReflectionMethod($this, 'run');
        $collection = [
            0 => [],
            1 => [],
        ];
        foreach ($reflection->getParameters() as $reflectionParameter) {
            $attribute = $this->getAttribute($reflectionParameter);
            $default = $this->getDefaultValue($reflectionParameter);
            $namedType = $reflectionParameter->getType();
            if ($namedType === null) {
                throw new TypeError(
                    message: message('Missing type declaration for parameter %parameter%')
                        ->withStrtr('%parameter%', '$' . $reflectionParameter->getName())
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
            $pos = intval(!$reflectionParameter->isOptional());
            $collection[$pos][$reflectionParameter->getName()] = $parameter;
        }

        return (new Parameters())->withAdded(...$collection[1])
            ->withAddedOptional(...$collection[0]);
    }

    final protected function getAttribute(ReflectionParameter $parameter): ParameterAttribute
    {
        $reflectionAttributes = $parameter->getAttributes(ParameterAttribute::class);
        /**
         * @phpstan-ignore-next-line
         * @var ?ReflectionAttribute $reflectionAttribute
         */
        $reflectionAttribute = $reflectionAttributes[0] ?? null;
        if ($reflectionAttribute !== null) {
            /** @var ParameterAttribute */
            return $reflectionAttribute->newInstance();
        }

        return new ParameterAttribute();
    }

    final protected function assertRunMethod(): void
    {
        if (!method_exists($this, 'run')) {
            throw new LogicException(
                message('Action %action% does not define a run method')
                    ->withCode('%action%', $this::class)
            );
        }
    }

    final protected function getDefaultValue(ReflectionParameter $reflection): mixed
    {
        return $reflection->isDefaultValueAvailable()
            ? $reflection->getDefaultValue()
            : null;
    }

    final protected function getParameterWithSome(
        ParameterInterface $parameter,
        ParameterAttribute $attribute
    ): ParameterInterface {
        if (!($parameter instanceof StringParameterInterface)) {
            return $parameter;
        }

        return $parameter->withRegex($attribute->regex());
    }

    final protected function getTypeToParameter(ReflectionParameter $reflection): string
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
