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

namespace Chevere\Components\Controller;

use InvalidArgumentException;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use Chevere\Components\Message\Message;
use Chevere\Components\Controller\Interfaces\ControllerInterface;

/**
 * ArgumentsWrap provides a object oriented way to retrieve typehinted arguments for the controller.
 */
final class ArgumentsWrap
{
    /** @var ControllerInterface */
    private $controller;

    /** @var array Passed callable arguments */
    private $arguments;

    /** @var ReflectionFunctionAbstract */
    private $reflection;

    /** @var array Typehinted arguments ready to use */
    private $typedArguments;

    public function __construct(ControllerInterface $controller, array $arguments)
    {
        $this->controller = $controller;
        $this->arguments = $arguments;
        $this->processArguments();
    }

    public function typedArguments(): array
    {
        return $this->typedArguments;
    }

    private function processArguments()
    {
        $this->reflection = new ReflectionMethod($this->controller, '__invoke');
        $this->typedArguments = [];
        $parameterIndex = 0;
        // Magically create typehinted arguments
        foreach ($this->reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (!isset($this->arguments[$name])) {
                throw new InvalidArgumentException(
                    (new Message('Unmatched argument %argument% in %controller%'))
                        ->code('%argument%', $name)
                        ->code('%controller%', get_class($this->controller) . '::__invoke')
                        ->toString()
                );
            }
            $type = null;
            $parameterType = $parameter->getType();
            if (isset($parameterType)) {
                $type = $parameterType->getName();
            }
            $this->processTypedArgument(
                $parameter,
                $type,
                $this->arguments[$name] ?? $this->arguments[$parameterIndex] ?? null
            );
            ++$parameterIndex;
        }
    }

    private function processTypedArgument(ReflectionParameter $parameter, string $type = null, $value = null): void
    {
        if (!isset($type) || in_array($type, Controller::TYPE_DECLARATIONS)) {
            $this->typedArguments[] = $value ?? ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null);
        } elseif (null === $value && $parameter->allowsNull()) {
            $this->typedArguments[] = null;

            return;
        }
        $this->typedArguments[] = new $type($value);
    }
}
