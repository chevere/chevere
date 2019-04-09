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
use ReflectionClass;

// use Roave\BetterReflection\BetterReflection;

class ApiEndpoint
{
    /** @var string The Controller interface */
    const CONTROLLER_INTERFACE = Interfaces\ControllerInterface::class;

    const CONST_DESCRIPTION = 'DESCRIPTION';
    const CONST_RESOURCES = 'RESOURCES';

    /** @var string The class name, passed in the constructor */
    protected $className;

    /** @var string The class name without its namespace */
    protected $classShortName;

    /** @var string The HTTP METHOD tied to the passed $className */
    protected $httpMethod;

    /** @var ReflectionClass The reflected controller class */
    protected $reflection;

    /** @var string|null Endpoint description taken from const DESCRIPTION */
    protected $description;

    /** @var array|null Endpoint resources taken from const RESOURCES */
    protected $resources;

    /** @var bool True if the controller class must implement RESOURCES. Prefixed Classes (_ClassName) won't be resourced. */
    protected $mustBeResourced;

    /**
     * @param string $className A className implementing the ControllerInterface
     */
    public function __construct(string $className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->className = $this->reflection->getName();
        $this->classShortName = $this->reflection->getShortName();
        $this->mustBeResourced = !Utils\Str::startsWith(Apis::METHOD_ROOT_PREFIX, $this->classShortName);
        $this->httpMethod = Utils\Str::replaceFirst(Apis::METHOD_ROOT_PREFIX, null, $this->classShortName);
        $this->assertInterface();
        $this->description = $this->reflection->hasConstant(static::CONST_DESCRIPTION) ? $this->reflection->getConstant(static::CONST_DESCRIPTION) : null;
        $this->resources = $this->reflection->hasConstant(static::CONST_RESOURCES) ? $this->reflection->getConstant(static::CONST_RESOURCES) : null;
        $this->assertConstResource();

        return $this;
    }

    /**
     * Throws a logic exception if the passed interface is not implemented in the reflected class.
     */
    protected function assertInterface(): void
    {
        if (!$this->reflection->implementsInterface(static::CONTROLLER_INTERFACE)) {
            throw new LogicException(
                (string)
                    (new Message('Class %s must implement the %i interface.'))
                        ->code('%s', $this->reflection->getName())
                        ->code('%i', static::CONTROLLER_INTERFACE)
            );
        }
    }

    /**
     * Throws a LogicException if the usage of const RESOURCES is invalid.
     */
    protected function assertConstResource(): void
    {
        $this->assertConstResourceNeed();
        $this->assertConstResourceType();
        $this->assertConstResourceMissed();
        $this->assertConstResourceValid();
    }

    /**
     * Throws a LogicException if const RESOURCES is set but not needed.
     */
    protected function assertConstResourceNeed(): void
    {
        if (isset($this->resources) && !$this->mustBeResourced) {
            throw new LogicException(
                (string)
                    (new Message('Class %s defines %r but this Controller class targets a non-resourced endpoint: %e. Remove the unused %r declaration.'))
                        ->code('%s', $this->className)
                        ->code('%r', 'const '.static::CONST_RESOURCES)
                        ->code('%e', $this->httpMethod.' api/users')
            );
        }
    }

    /**
     * Throws a LogicException if const RESOURCES doesn't match the expected type.
     */
    protected function assertConstResourceType(): void
    {
        if (isset($this->resources) && $this->mustBeResourced && !is_array($this->resources)) {
            throw new LogicException(
                (string)
                    (new Message('Class %s must define %r of type %t, %f found.'))
                        ->code('%s', $this->className)
                        ->code('%r', 'const '.static::CONST_RESOURCES)
                        ->code('%t', 'array')
                        ->code('%f', gettype($this->resources))
            );
        }
    }

    /**
     * Throws a LogicException if RESOURCES are needed but missed.
     */
    protected function assertConstResourceMissed(): void
    {
        if (!isset($this->resources) && $this->mustBeResourced) {
            throw new LogicException(
                (string)
                    (new Message('Class %s must define %r.'))
                        ->code('%s', $this->className)
                        ->code('%r', 'const '.static::CONST_RESOURCES)
            );
        }
    }

    /**
     * Throws a LogicException if RESOURCES maps to invalid classes.
     */
    protected function assertConstResourceValid(): void
    {
        if (isset($this->resources)) {
            foreach ($this->resources as $propertyName => $className) {
                if (!class_exists($className)) {
                    throw new LogicException(
                        (string)
                            (new Message('Class %s not found for %c Controller.'))
                                ->code('%s', $className)
                                ->code('%c', $this->className)
                    );
                }
            }
        }
    }
}
