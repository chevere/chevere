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

    /** @var array explode('::', $callableHandle) */
    protected $callableHandleMethodExplode;

    /** @var string The callable string handle used to construct the object */
    public $callableHandle;

    /** @var array An array containg typehinted arguments ready to use */
    private $typedArguments;

    /** @var ReflectionMethod The reflected callable (method) */
    protected $reflectionMethod;

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

    /** @var ReflectionFunction The reflected callable (function) */
    protected $reflectionFunction;

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
            $this->processCallable();
        } else {
            if (class_exists($callableHandle)) {
                if (method_exists($callableHandle, '__invoke')) {
                    $this->callable = new $callableHandle();
                    $this->class = $callableHandle;
                    $this->method = '__invoke';
                    $this->isAnon = false;
                    $this->processCallable();
                } else {
                    throw new LogicException(
                        (string)
                            (new Message('Missing magic method %s in class %c.'))
                                ->code('%s', '__invoke')
                                ->code('%c', $callableHandle)
                    );
                }
            }
        }
        // Some work needed when dealing with fileHandle
        if (!isset($this->callable)) {
            if (Utils\Str::contains('::', $this->callableHandle)) {
                $this->callableHandleMethodExplode = explode('::', $this->callableHandle);
                $this->handleCallableClass($this->callableHandleMethodExplode);
            } else {
                $this->handleCallableFile($this->callableHandle);
                $this->processCallable();
            }
        }
    }

    protected function handleCallableClass(array $explode, string $callableHandle): void
    {
        $class = $explode[0];
        $callableHandle = implode('::', $callableHandle);
        if (!class_exists($class)) {
            throw new LogicException(
                (string)
                    (new Message('Callable string %s is targeting not found class %c.'))
                        ->code('%s', $callableHandle)
                        ->code('%c', $class)
            );
        }
        $method = $explode[1];
        if (0 === strpos($method, '__')) {
            throw new LogicException(
                (string)
                    (new Message('Callable string %s is targeting the magic method %m.'))
                        ->code('%s', $callableHandle)
                        ->code('%m', $method)
            );
        }
        if (!method_exists($class, $method)) {
            throw new LogicException(
                (string)
                    (new Message('Callable string %s is targeting an nonexistent method %m.'))
                        ->code('%s', $callableHandle)
                        ->code('%m', $method)
            );
        }
    }

    protected function handleCallableFile(string $callableHandle): void
    {
        $callableFilepath = Path::fromHandle($callableHandle);
        if (!File::exists($callableFilepath)) {
            throw new LogicException(
                (string)
                    (new Message('Unable to locate any callable specified by %s.'))
                        ->code('%s', $callableHandle)
            );
        }
        $callable = include $callableFilepath;
        if (!is_callable($callable)) {
            throw new LogicException(
                (string)
                    (new Message('Expected %s callable, %t provided in %f.'))
                        ->code('%s', '$callable')
                        ->code('%t', gettype($callable))
                        ->code('%f', $callableHandle)
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
                (string)
                    (new Message('Invalid type %s, expecting one of these: %v.'))
                        ->code('%s', $type)
                        ->code('%v', implode(', ', static::TYPES))
            );
        }
    }

    // Process the callable and fill the object properties
    protected function processCallable()
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
                if (isset($this->callableHandleMethodExplode)) {
                    $this->class = $this->callableHandleMethodExplode[0];
                    $this->method = $this->callableHandleMethodExplode[1];
                }
            }
        }
        $this->validateType($this->type);
    }

    /**
     * Undocumented function.
     *
     * @return array an array containing ReflectionParameter members
     */
    public function getParameters(): ?array
    {
        if (!isset($this->parameters)) {
            $this->processParameters();
        }

        return $this->parameters ?? null;
    }

    protected function hasReflection(): bool
    {
        return isset($this->reflectionFunction) || isset($this->reflectionMethod);
    }

    public function getReflection(): \ReflectionFunctionAbstract
    {
        return isset($this->reflectionFunction)
            ? $this->reflectionFunction
            : $this->reflectionMethod;
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

        return $this->arguments ?? null;
    }

    protected function processReflection(): self
    {
        if ($this->hasReflection()) {
            return $this;
        }
        if (is_object($this->callable)) {
            $this->reflectionMethod = new ReflectionMethod($this->callable, $this->method);
        } else {
            $this->reflectionFunction = new ReflectionFunction($this->callable);
        }

        return $this;
    }

    protected function processParameters(): self
    {
        $this->processReflection();
        $reflection = $this->getReflection();
        $this->parameters = $reflection->getParameters();

        return $this;
    }

    protected function processArguments(): self
    {
        $this->processReflection();
        $this->typedArguments = [];
        $parameterIndex = 0;

        // Magically create typehinted arguments
        foreach ($this->getReflection()->getParameters() as $parameter) {
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
