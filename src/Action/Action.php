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
use Chevere\Attributes\DescriptionAttribute;
use Chevere\Attributes\RegexAttribute;
use Chevere\Common\Traits\DescriptionTrait;
use function Chevere\Message\message;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ObjectParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Parameters;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Exceptions\LogicException;
use ReflectionAttribute;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

abstract class Action implements ActionInterface
{
    use DescriptionTrait;

    protected string $description = '';

    protected ParametersInterface $parameters;

    protected ParametersInterface $responseParameters;

    public function __construct()
    {
        $this->setUp();
    }

    protected function setUp(): void
    {
        $this->description = $this->getDescription();
        $this->parameters = $this->getParameters();
        $this->responseParameters = $this->getResponseParameters();
    }

    final protected function getParameters(): ParametersInterface
    {
        try {
            $reflection = new ReflectionMethod($this, 'run');
        } catch (ReflectionException) {
            throw new LogicException(
                message('Action %action% does not provide %method% method')
                    ->code('%action%', $this::class)
                    ->code('%method%', 'run')
            );
        }
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

    private function getAttribute(ReflectionParameter $parameter, string $className): object
    {
        $reflectionAttributes = $parameter->getAttributes($className);
        /** @var ReflectionAttribute $reflectionAttribute */
        $reflectionAttribute = $reflectionAttributes[0] ?? null;
        if (isset($reflectionAttribute)) {
            return $reflectionAttribute->newInstance();
        }

        return new Attribute();
    }

    public function getResponseParameters(): ParametersInterface
    {
        return new Parameters();
    }

    final public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    final public function responseParameters(): ParametersInterface
    {
        return $this->responseParameters;
    }

    final public function getArguments(mixed ...$namedArguments): ArgumentsInterface
    {
        return new Arguments($this->parameters(), ...$namedArguments);
    }

    final public function getResponse(mixed ...$namedData): ResponseInterface
    {
        new Arguments($this->responseParameters, ...$namedData);

        return new Response(...$namedData);
    }
}
