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
use Chevere\Common\Traits\DescriptionTrait;
use function Chevere\Message\message;
use Chevere\Parameter\Arguments;
use Chevere\Parameter\Attributes\ParameterAttribute;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Parameters;
use Chevere\Response\Interfaces\ResponseInterface;
use Chevere\Response\Response;
use Chevere\Throwable\Exceptions\LogicException;
use ReflectionAttribute;
use ReflectionException;
use ReflectionMethod;

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
            $description = '';
            /** @var ParameterAttribute[] $parameterAttributes */
            $parameterAttributes = $parameter->getAttributes(ParameterAttribute::class);
            /** @var ReflectionAttribute $reflectionAttribute */
            $reflectionAttribute = $parameterAttributes[0] ?? null;
            if (isset($reflectionAttribute)) {
                /** @var ParameterAttribute $parameterAttribute */
                $parameterAttribute = $reflectionAttribute->newInstance();
                $description = $parameterAttribute->description();
            }
            $default = $parameter->isDefaultValueAvailable()
                    ? $parameter->getDefaultValue()
                    : null;
            $typeName = $parameter->getType()->getName();
            $type = self::TYPES_TO_CLASSES[$typeName] ?? null;
            if ($type === null) {
                $type = self::TYPES_TO_CLASSES['object'];
            }
            $typedParam = new $type($description);
            
            if ($default !== null && method_exists($typedParam, 'withDefault')) {
                $typedParam = $typedParam->withDefault($default);
            }
            if (isset($parameterAttribute) && $typedParam instanceof StringParameterInterface) {
                $typedParam = $typedParam
                    ->withRegex($parameterAttribute->regex());
            }
            $pos = $parameter->isOptional() ? 0 : 1;
            $collection[$pos][$parameter->getName()] = $typedParam;
        }
            
        return $parameters->withAdded(...$collection[1])
            ->withAddedOptional(...$collection[0]);
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
