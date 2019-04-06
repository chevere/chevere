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

namespace Chevereto\Core;

use LogicException;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;

abstract class CallableWrapProcessor
{
    const TYPE_FUNCTION = 'function';
    const TYPE_METHOD = 'method';
    const TYPE_CLASS = 'class';

    // const SOURCES = [self::SOURCE_FUNCTION, self::SOURCE_METHOD, self::SOURCE_CLASS, self::SOURCE_FILEHANDLE];
    const TYPES = [self::TYPE_FUNCTION, self::TYPE_CLASS];

    /** @var array explode('::', $callableHandle) */
    protected $callableHandleMethodExplode;

    // is_array (function)
    // Chevereto\Core\Path::fromHandle (method)
    // Chevereto\Core\Controllers\ApiGet (class implementing invoke)
    // callables:index (fileHandle return callable)

    /** @var string The callable string handle used to construct the object */
    protected $callableHandle;

    /** @var array An array containg typehinted arguments ready to use */
    private $typedArguments;

    /** @var ReflectionMethod The reflected callable (method) */
    protected $reflectionMethod;

    /** @var callable The actual callable */
    protected $callable;

    /** @var string The callable file (if any) */
    protected $callableFilepath;

    /** @var string The callable type (function, method, class) */
    protected $type;

    /** @var string Class name (if any) */
    protected $class;

    /** @var string Method name (if any) */
    protected $method;

    /** @var ReflectionFunction The reflected callable (function) */
    protected $reflectionFunction;

    /** @var string[] Callable parameters */
    protected $parameters;

    /** @var array Callable arguments */
    protected $arguments;

    /** @var array Passed callable arguments */
    protected $passedArguments;

    /** @var bool True if the callable comes from a fileHandle */
    protected $isFileHandle;

    /** @var bool True if the callable represents a anon function or class */
    protected $isAnon;

    protected function processCallableHandle(): void
    {
        if (Utils\Str::contains('::', $this->callableHandle)) {
            $this->processCallableHandleMethod();
        } else {
            $this->processCallableHandleFile();
        }
    }

    protected function processCallableHandleMethod(): void
    {
        $this->callableHandleMethodExplode = explode('::', $this->callableHandle);
        $class = $this->callableHandleMethodExplode[0];
        if (!class_exists($class)) {
            throw new LogicException(
                (string)
                    (new Message('Callable string %s is targeting not found class %c.'))
                        ->code('%s', $this->callableHandle)
                        ->code('%c', $class)
            );
        }
        $method = $this->callableHandleMethodExplode[1];
        if (0 === strpos($method, '__')) {
            throw new LogicException(
                (string)
                    (new Message('Callable string %s is targeting the magic method %m.'))
                        ->code('%s', $this->callableHandle)
                        ->code('%m', $method)
            );
        }
        if (!method_exists($class, $method)) {
            throw new LogicException(
                (string)
                    (new Message('Callable string %s is targeting an nonexistent method %m.'))
                        ->code('%s', $this->callableHandle)
                        ->code('%m', $method)
            );
        }
    }

    protected function processCallableHandleFile(): void
    {
        $callableFilepath = Path::fromHandle($this->callableHandle);
        if (!File::exists($callableFilepath)) {
            throw new LogicException(
                (string)
                    (new Message('Unable to locate any callable specified by %s.'))
                        ->code('%s', $this->callableHandle)
            );
        }
        $callable = include $callableFilepath;
        if (!is_callable($callable)) {
            throw new LogicException(
                (string)
                    (new Message('Expected %s callable, %t provided in %f.'))
                        ->code('%s', '$callable')
                        ->code('%t', gettype($callable))
                        ->code('%f', $this->callableHandle)
            );
        }
        $this
            ->setIsFileHandle(true)
            ->setCallable($callable)
            ->setCallableFilepath($callableFilepath)
            ->prepare();
    }

    protected function processReflection(): self
    {
        if ($this->hasReflection()) {
            return $this;
        }
        if (is_object($this->getCallable())) {
            $this->reflectionMethod = new ReflectionMethod($this->getCallable(), $this->getMethod());
        } else {
            $this->reflectionFunction = new ReflectionFunction($this->getCallable());
        }

        return $this;
    }

    protected function processParameters(): self
    {
        $this->processReflection();
        $reflection = $this->getReflection();

        $this->setParameters($reflection->getParameters());

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
                $this->getPassedArguments()[$parameter->getName()] ?? $this->getPassedArguments()[$parameterIndex] ?? null
            );
            ++$parameterIndex;
        }
        $this->setArguments($this->typedArguments);

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
