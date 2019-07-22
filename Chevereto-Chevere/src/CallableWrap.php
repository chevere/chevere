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

namespace Chevereto\Chevere;

use LogicException;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;
use ReflectionClass;
use ReflectionFunctionAbstract;

/**
 * CallableWrap provides a object oriented way to interact with Chevereto\Core accepted callable strings.
 *
 * Accepted callable strings are:
 *
 * - A callable (function, method name)
 * - A class implementing ::__invoke
 * - A fileHandle string representing the path of a file wich returns a callable
 */
class CallableWrap
{
    const TYPE_FUNCTION = 'function';
    const TYPE_METHOD = 'method';
    const TYPE_CLASS = 'class';

    const TYPES = [self::TYPE_FUNCTION, self::TYPE_CLASS];

    /** @var string The callable string handle used to construct the object */
    public $callableHandle;

    /** @var array An array containg typehinted arguments ready to use */
    private $typedArguments;

    /** @var ReflectionMethod The reflected callable (method) */
    protected $reflectionMethod;

    /** @var ReflectionFunction The reflected callable (function) */
    protected $reflectionFunction;

    /** @var ReflectionFunctionAbstract */
    protected $reflection;

    /** @var callable The actual callable */
    public $callable;

    /** @var string The callable file (if any) */
    public $callableFilepath;

    /** @var string The callable type (function, method, class) */
    public $type;

    /** @var string Class name (if any) */
    public $class;

    /** @var string Method name (if any) */
    public $method;

    /** @var string[] Callable parameters */
    public $parameters;

    /** @var array Callable arguments */
    public $arguments;

    /** @var array Passed callable arguments */
    protected $passedArguments;

    /** @var bool True if the callable comes from a fileHandle */
    public $isFileHandle;

    /** @var bool True if the callable represents a anon function or class */
    public $isAnon;

    public function __construct(string $callableHandle)
    {
        $this->isFileHandle = false;
        $this->callableHandle = $callableHandle;
        // Direct processing for callable strings and invocable classes
        if (is_callable($this->callableHandle)) {
            $this->callable = $callableHandle;
            $this->isAnon = false;
            $this->process();
        } else {
            if (class_exists($callableHandle)) {
                $this->handleCallableClass($callableHandle);
            }
        }
        // Some work needed when dealing with fileHandle
        if (!isset($this->callable)) {
            if (Utility\Str::contains('::', $this->callableHandle)) {
                $explode = explode('::', $this->callableHandle);
                $this->class = $explode[0];
                $this->method = $explode[1];
                $this->handleCallableClassMethod($this->class, $this->method);
            } else {
                $this->handleCallableFile($this->callableHandle);
                $this->process();
            }
        }
    }

    protected function handleCallableClass(string $callableClass): void
    {
        if (method_exists($callableClass, '__invoke')) {
            $this->callable = new $callableClass();
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
        $this->isFileHandle = true;
        $this->callable = $callable;
        $this->callableFilepath = $callableFilepath;
    }

    protected function validateType(string $type)
    {
        if (!in_array($type, static::TYPES)) {
            throw new LogicException(
                (new Message('Invalid type %s, expecting one of these: %v.'))
                    ->code('%s', $type)
                    ->code('%v', implode(', ', static::TYPES))
                    ->toString()
            );
        }
    }

    // Process the callable and fill the object properties
    protected function process()
    {
        if (isset($this->class)) {
            $this->type = isset($this->method) ? static::TYPE_CLASS : static::TYPE_FUNCTION;
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
        $this->validateType($this->type);
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

    protected function processReflection(): self
    {
        if ($this->reflectionFunction) {
            return $this;
        }
        if (is_object($this->callable)) {
            $this->reflectionMethod = new ReflectionMethod($this->callable, $this->method);
        } else {
            $this->reflectionFunction = new ReflectionFunction($this->callable);
        }
        $this->reflection = $this->reflectionFunction ?? $this->reflectionMethod;

        return $this;
    }

    protected function processParameters(): self
    {
        $this->processReflection();
        $this->parameters = $this->reflection->getParameters();

        return $this;
    }

    protected function processArguments(): self
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

        return $this;
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
