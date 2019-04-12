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

/**
 * Provides information about any Controller implementing the Interfaces\ControllerInterface interface.
 */
class ControllerInspect implements Interfaces\ToArrayInterface
{
    /** @var string The Controller interface */
    const INTERFACE_CONTROLLER = Interfaces\ControllerInterface::class;

    /** @var string The Controller interface */
    const INTERFACE_CONTROLLER_RESOURCE = Interfaces\ControllerResourceInterface::class;

    /** @var string The CreateFromString interface */
    const INTERFACE_CREATE_FROM_STRING = Interfaces\CreateFromString::class;

    /** @var string The description property name */
    const PROP_DESCRIPTION = 'description';

    /** @var string The resource property name */
    const PROP_RESOURCES = 'resources';

    /** @var string The class name, passed in the constructor */
    protected $className;

    /** @var string|null The HTTP METHOD tied to the passed $className */
    protected $httpMethod;

    /** @var ReflectionClass The reflected controller class */
    protected $reflection;

    /** @var string|null Endpoint description taken from const DESCRIPTION */
    protected $description;

    /** @var array|null Endpoint resources taken from const RESOURCES */
    protected $resources;

    /** @var bool True if the controller class must implement RESOURCES. Prefixed Classes (_ClassName) won't be resourced. */
    protected $useResource;

    /** @var array|null Instructions for creating resources from string [propname => [regex, description],] */
    protected $resourcesFromString;

    /** @var string The path component associated with the inspected Controller, used by Api */
    protected $pathComponent;

    /**
     * @param string $className A class name implementing the ControllerInterface
     */
    public function __construct(string $className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->className = $this->reflection->getName();
        $this->assertControllerInterface();
        $this->filepath = $this->reflection->getFileName();
        $classShortName = $this->reflection->getShortName();
        $this->useResource = !Utils\Str::startsWith(Api::METHOD_ROOT_PREFIX, $classShortName);
        $this->assertControllerResourceInterface();
        $this->httpMethod = Utils\Str::replaceFirst(Api::METHOD_ROOT_PREFIX, null, $classShortName);
        $this->description = $className::getDescription();
        $this->resources = $className::getResources();
        $this->parameters = $className::getParameters();
        $this->assertConstResource();
        $this->assertProcessResources();
        $this->processPathComponent();
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
                    $resourcesFromString[$propName] = [
                        'regex' => $resourceReflection->getStaticPropertyValue('stringRegex'),
                        'description' => $resourceReflection->getStaticPropertyValue('stringDescription'),
                    ];
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
                    (new Message('Class %s must implement the %i interface at %f.'))
                        ->code('%s', $this->reflection->getName())
                        ->code('%i', static::INTERFACE_CONTROLLER)
                        ->code('%f', $this->reflection->getFileName())
            );
        }
    }

    protected function assertControllerResourceInterface(): void
    {
        if ($this->useResource && !$this->reflection->implementsInterface(static::INTERFACE_CONTROLLER_RESOURCE)) {
            throw new LogicException(
                (string)
                    (new Message('Class %s must implement the %i interface at %f.'))
                        ->code('%s', $this->reflection->getName())
                        ->code('%i', static::INTERFACE_CONTROLLER_RESOURCE)
                        ->code('%f', $this->reflection->getFileName())
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
        if (isset($this->resources) && !$this->useResource) {
            throw new LogicException(
                (string)
                    (new Message('Class %s defines %r but this Controller class targets a non-resourced endpoint: %e. Remove the unused %r declaration at %f.'))
                        ->code('%s', $this->className)
                        ->code('%r', 'const '.static::PROP_RESOURCES)
                        ->code('%e', $this->httpMethod.' api/users')
                        ->code('%f', $this->filepath)
            );
        }
    }

    /**
     * Throws a LogicException if const RESOURCES doesn't match the expected type.
     */
    protected function assertConstResourceType(): void
    {
        if (isset($this->resources) && $this->useResource && !is_array($this->resources)) {
            throw new LogicException(
                (string)
                    (new Message('Class %s must define %r of type %t, %x found at %f.'))
                        ->code('%s', $this->className)
                        ->code('%r', 'const '.static::PROP_RESOURCES)
                        ->code('%t', 'array')
                        ->code('%x', gettype($this->resources))
                        ->code('%f', $this->filepath)
            );
        }
    }

    /**
     * Throws a LogicException if RESOURCES are needed but missed.
     */
    protected function assertConstResourceMissed(): void
    {
        if (!isset($this->resources) && $this->useResource) {
            throw new LogicException(
                (string)
                    (new Message('Class %s must define %r at %f.'))
                        ->code('%s', $this->className)
                        ->code('%r', 'const '.static::PROP_RESOURCES)
                        ->code('%f', $this->filepath)
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
                            (new Message('Class %s not found for %c Controller at %f.'))
                                ->code('%s', $className)
                                ->code('%c', $this->className)
                                ->code('%f', $this->filepath)
                    );
                }
            }
        }
    }

    /**
     * Sets $pathComponent based on the inspected values.
     */
    protected function processPathComponent(): void
    {
        /** @var string The class namespace, like App\Api\Users for class App\Api\Users\DELETE */
        $classNs = dirname($this->className);
        /** @var string The class namespace without App (Api\Users) */
        $classNsNoApp = Utils\Str::replaceFirst(APP_NS_HANDLE, null, $classNs);
        /** @var string The class namespace without App, lowercased with forward slashes. */
        $pathComponent = strtolower(Utils\Str::forwardSlashes($classNsNoApp));
        if ($this->useResource) {
            /** @var string The Controller resource wildcard path component {resource} */
            $resourceWildcard = '{'.array_keys($this->resources)[0].'}';
            $pathComponent .= '/'.$resourceWildcard;
        }
        $this->pathComponent = $pathComponent;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getHttpMethod(): ?string
    {
        return $this->httpMethod;
    }

    public function getReflection(): ReflectionClass
    {
        return $this->reflection;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getResources(): ?array
    {
        return $this->resources;
    }

    public function useResource(): bool
    {
        return $this->useResource;
    }

    public function getResourcesFromString(): ?array
    {
        return $this->resourcesFromString;
    }

    public function getPathComponent(): string
    {
        return $this->pathComponent;
    }

    public function toArray(): array
    {
        return [
            'className' => $this->className,
            'httpMethod' => $this->httpMethod,
            'description' => $this->description,
            'resources' => $this->resources,
            'useResource' => $this->useResource,
            'resourcesFromString' => $this->resourcesFromString,
            'pathComponent' => $this->pathComponent,
        ];
    }
}
