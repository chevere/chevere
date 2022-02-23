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
use Chevere\Common\Attributes\DescriptionAttribute;
use Chevere\Common\Traits\DescriptionTrait;
use Chevere\Container\Container;
use function Chevere\Message\message;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
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

    protected string $description;

    protected ParametersInterface $parameters;

    protected ParametersInterface $responseParameters;

    protected ParametersInterface $containerParameters;

    protected ContainerInterface $container;

    public function __construct()
    {
        $this->parameters = $this->parameters();
        $this->containerParameters = $this->containerParameters();
    }

    public function getContainerParameters(): ParametersInterface
    {
        return new Parameters();
    }
    
    final public function containerParameters(): ParametersInterface
    {
        return $this->containerParameters ??= $this->getContainerParameters();
    }

    public function getResponseParameters(): ParametersInterface
    {
        return new Parameters();
    }

    final public function parameters(): ParametersInterface
    {
        return $this->parameters ??= $this->getParameters();
    }

    final public function responseParameters(): ParametersInterface
    {
        return $this->responseParameters ??= $this->getResponseParameters();
    }

    final public function getArguments(mixed ...$namedArguments): ArgumentsInterface
    {
        return new Arguments($this->parameters(), ...$namedArguments);
    }

    final protected function getResponse(mixed ...$namedData): ResponseInterface
    {
        $arguments = new Arguments($this->responseParameters(), ...$namedData);

        return new Response(...$arguments->toArray());
    }

    final public function withContainer(ContainerInterface $container): ActionInterface
    {
        $new = clone $this;
        $new->container = $container;

        return $new;
    }

    final public function container(): ContainerInterface
    {
        return $this->container ??= new Container();
    }

    final public function runner(mixed ...$namedArguments): ResponseInterface
    {
        $this->assertRunMethod();
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
                message('The container does not provide the parameter(s): [%missing%]')
                    ->strtr('%missing%', implode(', ', $missingService))
            );
        }
        $arguments = $this->getArguments(...$namedArguments)->toArray();

        return $this->run(...$arguments);
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
        foreach ($reflection->getParameters() as $parameter) {
            $descriptionAttribute = $this->getAttribute(
                $parameter,
                DescriptionAttribute::class
            );
            $description = $descriptionAttribute instanceof DescriptionAttribute
                ? $descriptionAttribute->description()
                : '';
            $default = $parameter->isDefaultValueAvailable()
                    ? $parameter->getDefaultValue()
                    : null;
            $typeName = $parameter->getType()->getName();
            $type = self::TYPE_TO_PARAMETER[$typeName] ?? null;
            if ($type === null) {
                $type = self::TYPE_TO_PARAMETER['object'];
            }
            $typedParam = new $type($description);
            if ($typedParam instanceof ObjectParameterInterface) {
                $typedParam = $typedParam->withClassName($typeName);
            }
            if ($default !== null && method_exists($typedParam, 'withDefault')) {
                $typedParam = $typedParam->withDefault($default);
            }
            if ($typedParam instanceof StringParameterInterface) {
                $regexAttribute = $this->getAttribute(
                    $parameter,
                    RegexAttribute::class
                );
                if ($regexAttribute instanceof RegexAttribute) {
                    $typedParam = $typedParam->withRegex($regexAttribute->regex());
                }
            }
            $pos = $parameter->isOptional() ? 0 : 1;
            $collection[$pos][$parameter->getName()] = $typedParam;
        }
            
        return $parameters->withAdded(...$collection[1])
            ->withAddedOptional(...$collection[0]);
    }

    protected function getAttribute(ReflectionParameter $parameter, string $className): object
    {
        $reflectionAttributes = $parameter->getAttributes($className);
        /** @var ReflectionAttribute $reflectionAttribute */
        $reflectionAttribute = $reflectionAttributes[0] ?? null;
        if (isset($reflectionAttribute)) {
            return $reflectionAttribute->newInstance();
        }

        return new Attribute();
    }

    protected function assertRunMethod(): void
    {
        if (!method_exists($this, 'run')) {
            throw new LogicException(
                message('Action %action% does not define a run method')
                    ->code('%action%', $this::class)
            );
        }
    }
}
