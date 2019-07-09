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

    /** @var string Absolute path to the inspected class */
    protected $filepath;

    /** @var string|null The HTTP METHOD tied to the passed $className */
    protected $httpMethod;

    /** @var ReflectionClass The reflected controller class */
    protected $reflection;

    /** @var string|null Controller description */
    protected $description;

    /** @var array|null Controller resources */
    protected $resources;

    /** @var array|null Controller parameters */
    protected $parameters;

    /** @var string|null Controller related resource (if any) */
    protected $relatedResource;

    /** @var string|null Controller relationship class (if any) */
    protected $relationship;

    /** @var bool True if the controller class must implement RESOURCES. Prefixed Classes (_ClassName) won't be resourced. */
    protected $useResource;

    /** @var array|null Instructions for creating resources from string [propname => [regex, description],] */
    protected $resourcesFromString;

    /** @var string The path component associated with the inspected Controller, used by Api */
    protected $pathComponent;

    /** @var string|null Same as $pathComponent but for the related relationship URL (if any) */
    protected $relationshipPathComponent;

    /** @var bool True if the inspected Controller implements Interfaces\ControllerResourceIterface */
    protected $isResource;

    /** @var bool True if the inspected Controller implements Interfaces\ControllerRelationshipIterface */
    protected $isRelatedResource;

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
        $this->parameters = $className::getParameters();
        $this->handleConstResource();
        $this->handleProcessResources();
        $this->processPathComponent();
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

    public function getRelatedResource(): ?string
    {
        return $this->relatedResource ?? null;
    }

    public function getRelationship(): ?string
    {
        return $this->relationship ?? null;
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

    /**
     * @return mixed string if the inspected controller is a related resource, null if otherwise
     */
    public function getRelationshipPathComponent(): ?string
    {
        return $this->relationshipPathComponent ?? null;
    }

    public function isResource(): bool
    {
        return $this->isResource;
    }

    public function isRelatedResource(): bool
    {
        return $this->isRelatedResource;
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
     * Process the Controller,.
     */
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
    protected function handleConstResource(): void
    {
        $this->handleConstResourceNeed();
        $this->handleConstResourceType();
        $this->handleConstResourceMissed();
        $this->handleConstResourceValid();
    }

    /**
     * Throws a LogicException if const RESOURCES is set but not needed.
     */
    protected function handleConstResourceNeed(): void
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
    protected function handleConstResourceType(): void
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
    protected function handleConstResourceMissed(): void
    {
        if (!isset($this->resources) && $this->isResource) {
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
    protected function handleConstResourceValid(): void
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
        $classNs = Utils\Str::replaceLast('\\'.$this->reflection->getShortName(), null, $this->className);
        /** @var string The class namespace without App (Api\Users) */
        $classNsNoApp = Utils\Str::replaceFirst(APP_NS_HANDLE, null, $classNs);
        /** @var string The class namespace without App, lowercased with forward slashes (api/users) */
        $pathComponent = strtolower(Utils\Str::forwardSlashes($classNsNoApp));
        /** @var array Exploded / $pathComponent */
        $pathComponents = explode('/', $pathComponent);
        if ($this->useResource) {
            /** @var string The Controller resource wildcard path component {resource} */
            $resourceWildcard = '{'.array_keys($this->resources)[0].'}';
            if ($this->isResource) {
                // Append the resource wildcard: api/users/{wildcard}
                $pathComponent .= '/'.$resourceWildcard;
            } elseif ($this->isRelatedResource) {
                /** @var string Pop the last path component, in this case a related resource */
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
                /** @var string Something like api/users/{user}/relationships/friends */
                $relatedPathComponent = implode('/', $relatedPathComponentArray);
                // $ROUTE_MAP[$relatedPathComponent]['GET'] = $this->className;
                $this->relationshipPathComponent = $relatedPathComponent;
                // ->implementsInterface(Interfaces\ControllerRelationshipInterface::class)
                $this->relationship = $this->reflection->getParentClass()->getName();
            }
        }
        $this->pathComponent = $pathComponent;
    }
}
