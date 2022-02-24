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

use Attribute;
use Chevere\Action\Interfaces\ActionInterface;
use Chevere\Action\Traits\ActionTrait;
use Chevere\Common\Attributes\DescriptionAttribute;
use Chevere\Common\Traits\DescriptionTrait;
use function Chevere\Message\message;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Parameters;
use Chevere\Regex\Attributes\RegexAttribute;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\LogicException;
use Psr\Container\ContainerInterface;
use ReflectionAttribute;
use ReflectionMethod;
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

    final public function runner(mixed ...$namedArguments): ResponseInterface
    {
        $this->assertContainer();
        $arguments = $this->getArguments(...$namedArguments)->toArray();
        $response = $this->run(...$arguments);
        if (!is_array($response)) {
            throw new LogicException(
                message('Method %method% must return an array.')
                    ->strtr('%method%', $this::class . '::run')
            );
        }

        return $this->getResponse(...$response);
    }

    final protected function getResponse(mixed ...$namedData): ResponseInterface
    {
        $arguments = new Arguments($this->responseParameters(), ...$namedData);

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
                    ->strtr('%action%', $this::class)
                    ->strtr('%missing%', implode(', ', $missingService))
            );
        }
    }

    final protected function getParameters(): ParametersInterface
    {
        $this->assertRunMethod();
        $reflection = new ReflectionMethod($this, 'run');
        $parameters = new Parameters();
        $collection = [
            0 => [],
            1 => [],
        ];
        foreach ($reflection->getParameters() as $reflectionParameter) {
            $description = $this->getDescriptionAttribute($reflectionParameter);
            $default = $this->getDefaultValue($reflectionParameter);
            $typeName = $reflectionParameter->getType()->getName();
            $type = $this->getTypeToParameter($reflectionParameter);
            $parameter = new $type($description);
            if ($parameter instanceof ObjectParameterInterface) {
                $parameter = $parameter->withClassName($typeName);
            }
            if ($default !== null && method_exists($parameter, 'withDefault')) {
                $parameter = $parameter->withDefault($default);
            }
            $parameter = $this->getParameterWithSome($parameter, $reflectionParameter);
            $pos = intval(!$reflectionParameter->isOptional());
            $collection[$pos][$reflectionParameter->getName()] = $parameter;
        }

        return $parameters->withAdded(...$collection[1])
            ->withAddedOptional(...$collection[0]);
    }

    final protected function getAttribute(ReflectionParameter $parameter, string $className): object
    {
        $reflectionAttributes = $parameter->getAttributes($className);
        /** @var ReflectionAttribute $reflectionAttribute */
        $reflectionAttribute = $reflectionAttributes[0] ?? null;
        if (isset($reflectionAttribute)) {
            return $reflectionAttribute->newInstance();
        }

        return new Attribute();
    }

    final protected function assertRunMethod(): void
    {
        if (!method_exists($this, 'run')) {
            throw new LogicException(
                message('Action %action% does not define a run method')
                    ->code('%action%', $this::class)
            );
        }
    }

    final protected function getDescriptionAttribute(ReflectionParameter $parameter): string
    {
        $attribute = $this->getAttribute(
            $parameter,
            DescriptionAttribute::class
        );

        return $attribute instanceof DescriptionAttribute
            ? $attribute->description()
            : '';
    }

    final protected function getDefaultValue(ReflectionParameter $reflection): mixed
    {
        return $reflection->isDefaultValueAvailable()
            ? $reflection->getDefaultValue()
            : null;
    }

    final protected function getParameterWithSome(
        ParameterInterface $parameter,
        ReflectionParameter $reflection
    ): ParameterInterface {
        if (!($parameter instanceof StringParameterInterface)) {
            return $parameter;
        }
        $attribute = $this->getAttribute(
            $reflection,
            RegexAttribute::class
        );
        if ($attribute instanceof RegexAttribute) {
            $parameter = $parameter->withRegex($attribute->regex());
        }

        return $parameter;
    }

    final protected function getTypeToParameter(ReflectionParameter $reflection): string
    {
        $typeName = $reflection->getType()->getName();
        $type = self::TYPE_TO_PARAMETER[$typeName] ?? null;
        if ($type === null) {
            $type = self::TYPE_TO_PARAMETER['object'];
        }
        
        return $type;
    }
}
