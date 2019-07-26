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

use LogicException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionClass;
use ReflectionFunctionAbstract;
use Chevere\Message;
use Chevere\Path;
use Chevere\File;

/**
 * Wrap provides a object oriented way to interact with Chevere controllers.
 *
 * Accepted callable strings are:
 *
 * - A callable (function, method name)
 * - A class implementing ::__invoke
 * - A fileHandle string representing the path of a file wich returns a callable
 */
class Wrap
{
    /** @var string The callable string handle used to construct the object */
    private $callableHandle;

    /** @var array An array containg typehinted arguments ready to use */
    private $typedArguments;

    /** @var ReflectionMethod The reflected callable (method) */
    private $reflectionMethod;

    /** @var ReflectionFunctionAbstract */
    private $reflection;

    /** @var callable The actual callable */
    private $callable;

    /** @var string The callable file (if any) */
    private $callableFilepath;

    /** @var string The callable type (function, method, class) */
    private $type;

    /** @var string Class name (if any) */
    private $class;

    /** @var string Method name (if any) */
    private $method;

    /** @var string[] Callable parameters */
    private $parameters;

    /** @var array Callable arguments */
    private $arguments;

    /** @var array Passed callable arguments */
    private $passedArguments;

    /** @var bool True if the callable represents a anon function or class */
    private $isAnon;

    public function __construct(string $callableHandle)
    {
        $this->callableHandle = $callableHandle;
        $this->handleCallableClass($callableHandle);
    }

    protected function handleCallableClass(string $callableClass): void
    {
        if (method_exists($callableClass, '__invoke')) {
            $this->callable = $callableClass;
            $this->class = $callableClass;
            $this->method = '__invoke';
            $this->isAnon = false;
            $this->process();
        } else {
            throw new LogicException(
                (new Message('Missing magic method %s in class %c.'))
                    ->code('%s', '__invoke')
                    ->code('%c', $callableClass)
                    ->toString()
            );
        }
    }

    protected function handleCallableClassMethod(string $class, string $method): void
    {
        if (!class_exists($class)) {
            throw new LogicException(
                (new Message('Callable string handle targeting not found class %c.'))
                    ->code('%c', $class)
                    ->toString()
            );
        }
        if (0 === strpos($method, '__')) {
            throw new LogicException(
                (new Message('Callable string handle targeting magic method %m.'))
                    ->code('%m', $method)
                    ->toString()
            );
        }
        if (!method_exists($class, $method)) {
            throw new LogicException(
                (new Message('Callable string handle targeting an nonexistent method %m.'))
                    ->code('%m', $method)
                    ->toString()
            );
        }
    }

    protected function handleCallableFile(string $callableHandle): void
    {
        $callableFilepath = Path::fromHandle($callableHandle);
        if (!File::exists($callableFilepath)) {
            throw new LogicException(
                (new Message('Unable to locate any callable specified by %s.'))
                    ->code('%s', $callableHandle)
                    ->toString()
            );
        }
        $callable = include $callableFilepath;
        if (!is_callable($callable)) {
            throw new LogicException(
                (new Message('Expected %s callable, %t provided in %f.'))
                    ->code('%s', '$callable')
                    ->code('%t', gettype($callable))
                    ->code('%f', $callableHandle)
                    ->toString()
            );
        }
        $this->callable = $callable;
        $this->callableFilepath = $callableFilepath;
    }

    // Process the callable and fill the object properties
    protected function process()
    {
        if (isset($this->class)) {
            $this->type = 'class';
        } else {
            if (is_object($this->callable)) {
                $this->method = '__invoke';
                $reflection = new ReflectionClass($this->callable);
                $this->type = static::TYPE_CLASS;
                $this->isAnon = $reflection->isAnonymous();
                $this->class = $this->isAnon ? 'class@anonymous' : $reflection->getName();
            } else {
                $this->type = static::TYPE_FUNCTION;
            }
        }
    }

    /**
     * Pass arguments to the callable which will be typehinted by this class.
     *
     * @param array $passedArguments
     *
     * @return self
     */
    public function setPassedArguments(array $passedArguments): self
    {
        $this->passedArguments = $passedArguments;

        return $this;
    }

    public function getArguments(): array
    {
        if (!isset($this->arguments)) {
            $this->processArguments();
        }

        return $this->arguments ?? [];
    }

    protected function processReflection()
    {
        $this->reflectionMethod = new ReflectionMethod($this->callable, $this->method);
        $this->reflection = $this->reflectionMethod;
    }

    protected function processParameters()
    {
        $this->processReflection();
        $this->parameters = $this->reflection->getParameters();
    }

    protected function processArguments()
    {
        $this->processReflection();
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
        // dd($this->reflection->getParameters());
        $this->arguments = $this->typedArguments;
    }

    protected function processTypedArgument(ReflectionParameter $parameter, string $type = null, $value = null): void
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
