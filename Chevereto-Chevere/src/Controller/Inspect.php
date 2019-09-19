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

namespace Chevere\Controller;

use LogicException;
use ReflectionClass;
use Chevere\Message\Message;
use Chevere\Api\Api;
use Chevere\Str\Str;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Controller\InspectContract;
use Chevere\Interfaces\ControllerResourceInterface;
use Chevere\Interfaces\CreateFromString;
use Chevere\Interfaces\ControllerRelationshipInterface;

/**
 * Provides information about any Controller implementing ControllerContract interface.
 */
final class Inspect implements InspectContract
{
    const METHOD_ROOT_PREFIX = Api::METHOD_ROOT_PREFIX;

    /** @var string The Controller interface */
    const INTERFACE_CONTROLLER = ControllerContract::class;

    /** @var string The Controller interface */
    const INTERFACE_CONTROLLER_RESOURCE = ControllerResourceInterface::class;

    /** @var string The CreateFromString interface */
    const INTERFACE_CREATE_FROM_STRING = CreateFromString::class;

    /** @var string The description property name */
    const PROP_DESCRIPTION = 'description';

    /** @var string The resource property name */
    const PROP_RESOURCES = 'resources';

    /** @var string The class name, passed in the constructor */
    public $className;

    /** @var string The class shortname name */
    public $classShortName;

    /** @var string Absolute path to the inspected class */
    public $filepath;

    /** @var string|null The HTTP METHOD tied to the passed $className */
    public $httpMethod;

    /** @var ReflectionClass The reflected controller class */
    public $reflection;

    /** @var string Controller description */
    public $description;

    /** @var array Controller parameters */
    public $parameters;

    /** @var array Controller resources */
    public $resources;

    /** @var string Controller related resource (if any) */
    public $relatedResource;

    /** @var string Controller relationship class (if any) */
    public $relationship;

    /** @var bool True if the controller class must implement RESOURCES. Prefixed Classes (_ClassName) won't be resourced. */
    public $useResource;

    /** @var array Instructions for creating resources from string [propname => [regex, description],] */
    public $resourcesFromString;

    /** @var string The path component associated with the inspected Controller, used by Api */
    public $pathComponent;

    /** @var string Same as $pathComponent but for the related relationship URL (if any) */
    public $relationshipPathComponent;

    /** @var bool True if the inspected Controller implements ControllerResourceIterface */
    public $isResource;

    /** @var bool True if the inspected Controller implements ControllerRelationshipIterface */
    public $isRelatedResource;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->className = $this->reflection->getName();
        $this->classShortName = $this->reflection->getShortName();
        $this->filepath = $this->reflection->getFileName();
        $this->isResource = $this->reflection->implementsInterface(ControllerResourceInterface::class);
        $this->isRelatedResource = $this->reflection->implementsInterface(ControllerRelationshipInterface::class);
        $this->useResource = $this->isResource || $this->isRelatedResource;
        $this->httpMethod = Str::replaceFirst(static::METHOD_ROOT_PREFIX, '', $this->classShortName);
        $this->description = $className::description();
        $this->handleResources($className);
        $this->parameters = $className::parameters();
        try {
            $this->handleControllerResourceInterface();
            $this->handleControllerInterface();
            $this->handleConstResourceNeed();
            $this->handleConstResourceMissed();
            $this->handleConstResourceValid();
        } catch (LogicException $e) {
            throw new LogicException(
                (new Message($e->getMessage()))
                    ->code('%interfaceController%', static::INTERFACE_CONTROLLER)
                    ->code('%reflectionName%', $this->reflection->getName())
                    ->code('%interfaceControllerResource%', static::INTERFACE_CONTROLLER_RESOURCE)
                    ->code('%reflectionFilename%', $this->reflection->getFileName())
                    ->code('%endpoint%', $this->httpMethod . ' api/users')
                    ->code('%className%', $this->className)
                    ->code('%propResources%', 'const ' . static::PROP_RESOURCES)
                    ->code('%filepath%', $this->filepath)
                    ->toString()
            );
        }
        $this->handleProcessResources();
        $this->processPathComponent();
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

    private function handleResources(string $className)
    {
        if ($this->isResource) {
            $this->resources = $className::resources();
        } elseif ($this->isRelatedResource) {
            $this->relatedResource = $className::getRelatedResource();
            if (empty($this->relatedResource)) {
                throw new LogicException(
                    (new Message('Class %s implements %i interface, but it doesnt define any related resource.'))
                        ->code('%s', $className)
                        ->code('%i', ControllerRelationshipInterface::class)
                        ->toString()
                );
            }
            $this->resources = $this->relatedResource::resources();
        }
    }

    private function handleControllerInterface(): void
    {
        if (!$this->reflection->implementsInterface(static::INTERFACE_CONTROLLER)) {
            throw new LogicException('Class %reflectionName% must implement the %interfaceController% interface at %reflectionFilename%.');
        }
    }

    private function handleControllerResourceInterface(): void
    {
        if (!Str::startsWith(static::METHOD_ROOT_PREFIX, $this->classShortName) && $this->useResource && !$this->reflection->implementsInterface(static::INTERFACE_CONTROLLER_RESOURCE)) {
            throw new LogicException('Class %reflectionName% must implement the %interfaceControllerResource% interface at %reflectionFilename%.');
        }
    }

    private function handleConstResourceNeed(): void
    {
        if (!empty($this->resources) && !$this->useResource) {
            throw new LogicException('Class %className% defines %propResources% but this Controller class targets a non-resourced endpoint: %endpoint%. Remove the unused %propResources% declaration at %filepath%.');
        }
    }

    private function handleConstResourceMissed(): void
    {
        if (null == $this->resources && $this->isResource) {
            throw new LogicException('Class %className% must define %propResources% at %filepath%.');
        }
    }

    private function handleConstResourceValid(): void
    {
        if (is_iterable($this->resources)) {
            foreach ($this->resources as $propertyName => $className) {
                if (!class_exists($className)) {
                    throw new LogicException(
                        (new Message('Class %s not found for %c Controller at %f.'))
                            ->code('%s', $className)
                            ->toString()
                    );
                }
            }
        }
    }

    private function handleProcessResources(): void
    {
        if (is_iterable($this->resources)) {
            $resourcesFromString = [];
            foreach ($this->resources as $propName => $resourceClassName) {
                // Better reflection is needed due to this: https://bugs.php.net/bug.php?id=69804
                // FIXME: Don't user BetterReflection as it adds PHPStorm stubs
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

    private function processPathComponent(): void
    {
        $pathComponent = $this->getPathComponent($this->className);
        $pathComponents = explode('/', $pathComponent);
        if ($this->useResource) {
            $resourceWildcard = '{' . array_keys($this->resources)[0] . '}';
            if ($this->isResource) {
                // Append the resource wildcard: api/users/{wildcard}
                $pathComponent .= '/' . $resourceWildcard;
            } elseif ($this->isRelatedResource) {
                $related = array_pop($pathComponents);
                // Inject the resource wildcard: api/users/{wildcard}/related
                $pathComponent = implode('/', $pathComponents) . '/' . $resourceWildcard . '/' . $related;
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
                // ->implementsInterface(ControllerRelationshipInterface::class)
                $this->relationship = $this->reflection->getParentClass()->getName();
            }
        }
        $this->pathComponent = $pathComponent;
    }

    private function getPathComponent(string $className): string
    {
        $classShortName = substr($className, strrpos($className, '\\') + 1);
        $classNamespace = Str::replaceLast('\\' . $classShortName, '', $className);
        $classNamespaceNoApp = Str::replaceFirst('App\\', '', $classNamespace);

        return strtolower(Str::forwardSlashes($classNamespaceNoApp));
    }
}
