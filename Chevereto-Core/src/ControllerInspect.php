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
use Roave\BetterReflection\BetterReflection;

// use Roave\BetterReflection\BetterReflection;

class ControllerInspect
{
    /** @var string The Controller interface */
    const INTERFACE_CONTROLLER = Interfaces\ControllerInterface::class;

    /** @var string The CreateFromString interface */
    const INTERFACE_CREATE_FROM_STRING = Interfaces\CreateFromString::class;

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
    protected $hasResource;

    /** @var array|null Instructions for creating resources from string [propname => [regex, description],] */
    protected $resourcesFromString;

    /**
     * @param string $className A className implementing the ControllerInterface
     */
    public function __construct(string $className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->className = $this->reflection->getName();
        $this->classShortName = $this->reflection->getShortName();
        $this->hasResource = !Utils\Str::startsWith(Apis::METHOD_ROOT_PREFIX, $this->classShortName);
        $this->httpMethod = Utils\Str::replaceFirst(Apis::METHOD_ROOT_PREFIX, null, $this->classShortName);
        $this->assertControllerInterface();
        $this->description = $this->reflection->hasConstant(static::CONST_DESCRIPTION)
            ? $this->reflection->getConstant(static::CONST_DESCRIPTION)
            : null;
        $this->resources = $this->reflection->hasConstant(static::CONST_RESOURCES)
            ? $this->reflection->getConstant(static::CONST_RESOURCES)
            : null;
        $this->assertConstResource();
        $this->assertProcessResources();
    }

    /**
     * Process the Controller,.
     */
    protected function assertProcessResources(): void
    {
        if ($this->resources) {
            $resourcesFromString = [];
            foreach ($this->resources as $propName => $resourceClassName) {
                // Better reflection is needed due to this: https://bugs.php.net/bug.php?id=69804
                $resourceReflection = (new BetterReflection())
                    ->classReflector()
                    ->reflect($resourceClassName);
                if ($resourceReflection->implementsInterface(static::INTERFACE_CREATE_FROM_STRING)) {
                    $resourcesFromString[$propName] = [$resourceReflection->getStaticPropertyValue('stringRegex'), $resourceReflection->getStaticPropertyValue('stringDescription')];
                }
            }
            if (!empty($resourcesFromString)) {
                $this->resourcesFromString = $resourcesFromString;
            }
        }
    }

    /**
     * Throws a logic exception if the passed interface is not implemented in the reflected class.
     */
    protected function assertControllerInterface(): void
    {
        if (!$this->reflection->implementsInterface(static::INTERFACE_CONTROLLER)) {
            throw new LogicException(
                (string)
                    (new Message('Class %s must implement the %i interface.'))
                        ->code('%s', $this->reflection->getName())
                        ->code('%i', static::INTERFACE_CONTROLLER)
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
        if (isset($this->resources) && !$this->hasResource) {
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
        if (isset($this->resources) && $this->hasResource && !is_array($this->resources)) {
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
        if (!isset($this->resources) && $this->hasResource) {
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
