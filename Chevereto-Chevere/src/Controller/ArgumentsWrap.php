<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Controller;

use ReflectionMethod;
use ReflectionParameter;
use ReflectionClass;
use ReflectionFunctionAbstract;
use Chevere\Interfaces\ControllerInterface;

/**
 * ArgumentsWrap provides a object oriented way to retrieve typehinted arguments for the controller.
 */
final class ArgumentsWrap
{
    /** @var array Typehinted arguments ready to use */
    private $typedArguments;

    /** @var ReflectionFunctionAbstract */
    private $reflection;

    /** @var array Passed callable arguments */
    private $passedArguments;

    /** @var array Usable arguments (FIXME: Better bame) */
    private $arguments;

    public function __construct(ControllerInterface $controller, array $arguments)
    {
        $this->controller = $controller;
        $reflection = new ReflectionClass($this->controller);
        $this->passedArguments = $arguments;
        if (isset($arguments)) {
            $this->processArguments();
        }
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    private function processArguments()
    {
        $this->reflection = new ReflectionMethod($this->controller, '__invoke');
        $this->typedArguments = [];
        $parameterIndex = 0;
        // Magically create typehinted arguments
        foreach ($this->reflection->getParameters() as $parameter) {
            $type = null;
            $parameterType = $parameter->getType();
            if (isset($parameterType)) {
                $type = $parameterType->getName();
            }
            $this->processTypedArgument(
                $parameter,
                $type,
                $this->passedArguments[$parameter->getName()] ?? $this->passedArguments[$parameterIndex] ?? null
            );
            ++$parameterIndex;
        }
        $this->arguments = $this->typedArguments;
    }

    private function processTypedArgument(ReflectionParameter $parameter, string $type = null, $value = null): void
    {
        if (!isset($type) || in_array($type, Controller::TYPE_DECLARATIONS)) {
            $this->typedArguments[] = $value ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
        } elseif (null === $value && $parameter->allowsNull()) {
            $this->typedArguments[] = null;
        } else {
            $this->typedArguments[] = new $type($value);
        }
    }
}
