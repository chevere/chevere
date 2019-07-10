<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Chevere;

use LogicException;
use ReflectionClass;
use Roave\BetterReflection\BetterReflection;

/**
 * Provides information about any Controller implementing Interfaces\ControllerInterface interface.
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
    public $className;

    /** @var string Absolute path to the inspected class */
    public $filepath;

    /** @var string|null The HTTP METHOD tied to the passed $className */
    public $httpMethod;

    /** @var ReflectionClass The reflected controller class */
    public $reflection;

    /** @var string|null Controller description */
    public $description;

    /** @var array|null Controller parameters */
    public $parameters;

    /** @var array|null Controller resources */
    public $resources;

    /** @var string|null Controller related resource (if any) */
    public $relatedResource;

    /** @var string|null Controller relationship class (if any) */
    public $relationship;

    /** @var bool True if the controller class must implement RESOURCES. Prefixed Classes (_ClassName) won't be resourced. */
    public $useResource;

    /** @var array|null Instructions for creating resources from string [propname => [regex, description],] */
    public $resourcesFromString;

    /** @var string The path component associated with the inspected Controller, used by Api */
    public $pathComponent;

    /** @var string|null Same as $pathComponent but for the related relationship URL (if any) */
    public $relationshipPathComponent;

    /** @var bool True if the inspected Controller implements Interfaces\ControllerResourceIterface */
    public $isResource;

    /** @var bool True if the inspected Controller implements Interfaces\ControllerRelationshipIterface */
    public $isRelatedResource;

    /**
     * @param string $className A class name implementing the ControllerInterface
     */
    public function __construct(string $className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->className = $this->reflection->getName();
        $this->handleControllerInterface();
        $this->filepath = $this->reflection->getFileName();
        $classShortName = $this->reflection->getShortName();
        $this->isResource = $this->reflection->implementsInterface(Interfaces\ControllerResourceInterface::class);
        $this->isRelatedResource = $this->reflection->implementsInterface(Interfaces\ControllerRelationshipInterface::class);
        $this->useResource = $this->isResource || $this->isRelatedResource;
        if (!Utils\Str::startsWith(Api::METHOD_ROOT_PREFIX, $classShortName)) {
            $this->handleControllerResourceInterface();
        }
        $this->httpMethod = Utils\Str::replaceFirst(Api::METHOD_ROOT_PREFIX, null, $classShortName);
        $this->description = $className::getDescription();
        $this->handleResources($className);
        $this->parameters = $className::getParameters();
        try {
            $this->handleConstResourceNeed();
            $this->handleConstResourceType();
            $this->handleConstResourceMissed();
            $this->handleConstResourceValid();
        } catch (Throwable $e) {
        }
        $this->handleProcessResources();
        $this->processPathComponent();
    }

    protected function handleResources(string $className)
    {
        if ($this->isResource) {
            $this->resources = $className::getResources();
        } elseif ($this->isRelatedResource) {
            $this->relatedResource = $className::getRelatedResource();
            if (!isset($this->relatedResource)) {
                throw new LogicException(
                    (string)
                        (new Message('Class %s implements %i interface, but it doesnt define any related resource.'))
                            ->code('%s', $className)
                            ->code('%i', Interfaces\ControllerRelationshipInterface::class)
                );
            }
            $this->resources = $this->relatedResource::getResources();
        }
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

    /**
     * Throws a logic exception if the passed interface is not implemented in the reflected class.
     */
    protected function handleControllerInterface(): void
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

    protected function handleControllerResourceInterface(): void
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

    /**
     * Throws a LogicException if const RESOURCES is set but not needed.
     */
    protected function handleConstResourceNeed(): void
    {
        if (isset($this->resources) && !$this->useResource) {
            throw new LogicException('Class %s defines %r but this Controller class targets a non-resourced endpoint: %e. Remove the unused %r declaration at %f.');
        }
        // '%s', $this->className
        // '%r', 'const '.static::PROP_RESOURCES
        // '%e', $this->httpMethod.' api/users'
        // '%f', $this->filepath
    }

    /**
     * Throws a LogicException if const RESOURCES doesn't match the expected type.
     */
    protected function handleConstResourceType(): void
    {
        if (isset($this->resources) && $this->useResource && !is_array($this->resources)) {
            throw new LogicException('Class %s must define %r of type %t, %x found at %f.');
        }
        // '%s', $this->className
        // '%r', 'const '.static::PROP_RESOURCES
        // '%t', 'array'
        // '%x', gettype($this->resources)
        // '%f', $this->filepath
    }

    /**
     * Throws a LogicException if RESOURCES are needed but missed.
     */
    protected function handleConstResourceMissed(): void
    {
        if (!isset($this->resources) && $this->isResource) {
            throw new LogicException('Class %s must define %r at %f.');
        }
        // '%s', $this->className
        // '%r', 'const '.static::PROP_RESOURCES
        // '%f', $this->filepath
    }

    /**
     * Throws a LogicException if RESOURCES maps to invalid classes.
     */
    protected function handleConstResourceValid(): void
    {
        if (isset($this->resources)) {
            foreach ($this->resources as $propertyName => $className) {
                if (!class_exists($className)) {
                    throw new LogicException(
                        (string)
                            (new Message('Class %s not found for %c Controller at %f.'))
                                ->code('%s', $className)
                    );
                }
            }
        }
        // '%c', $this->className
        // '%f', $this->filepath
    }

    protected function handleProcessResources(): void
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
     * Sets $pathComponent based on the inspected values.
     */
    protected function processPathComponent(): void
    {
        $classNamespace = Utils\Str::replaceLast('\\'.$this->reflection->getShortName(), null, $this->className);
        $classNamespaceNoApp = Utils\Str::replaceFirst(APP_NS_HANDLE, null, $classNamespace);
        $pathComponent = strtolower(Utils\Str::forwardSlashes($classNamespaceNoApp));
        $pathComponents = explode('/', $pathComponent);
        if ($this->useResource) {
            $resourceWildcard = '{'.array_keys($this->resources)[0].'}';
            if ($this->isResource) {
                // Append the resource wildcard: api/users/{wildcard}
                $pathComponent .= '/'.$resourceWildcard;
            } elseif ($this->isRelatedResource) {
                $related = array_pop($pathComponents);
                // Inject the resource wildcard: api/users/{wildcard}/related
                $pathComponent = implode('/', $pathComponents).'/'.$resourceWildcard.'/'.$related;
                /*
                * Code below generates api/users/{user}/relationships/friends (relationship URL)
                * from api/users/{user}/friends (related resource URL).
                */
                $pathComponentArray = explode('/', $pathComponent);
                $relationship = array_pop($pathComponentArray);
                $relatedPathComponentArray = array_merge($pathComponentArray, ['relationships'], [$relationship]);
                // Something like api/users/{user}/relationships/friends
                $relatedPathComponent = implode('/', $relatedPathComponentArray);
                $this->relationshipPathComponent = $relatedPathComponent;
                // ->implementsInterface(Interfaces\ControllerRelationshipInterface::class)
                $this->relationship = $this->reflection->getParentClass()->getName();
            }
        }
        $this->pathComponent = $pathComponent;
    }
}
