<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Controller;

use LogicException;
use ReflectionClass;
use Chevere\Components\Message\Message;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\InspectInterface;
use Chevere\Components\Str\Str;

/**
 * Provides information about any Controller implementing ControllerInterface interface.
 */
final class Inspect implements InspectInterface
{
    /** The Controller interface */
    const INTERFACE_CONTROLLER = ControllerInterface::class;

    /** @var string The Controller interface */
    // const INTERFACE_CONTROLLER_RESOURCE = ControllerResourceInterface::class;

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
     * Creates a new instance.
     *
     * @param string $className A class name implementing the ControllerInterface
     */
    public function __construct(string $className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->className = $this->reflection->getName();
        $this->classShortName = $this->reflection->getShortName();
        $this->filepath = $this->reflection->getFileName();
        // $this->isResource = $this->reflection->implementsInterface(ControllerResourceInterface::class);
        // $this->isRelatedResource = $this->reflection->implementsInterface(ControllerRelationshipInterface::class);
        $this->useResource = $this->isResource || $this->isRelatedResource;
        $this->httpMethod = $this->classShortName;
        $this->description = $className::description();
        $this->handleResources($className);
        $this->parameters = $className::parameters();
        try {
            // $this->handleControllerResourceInterface();
            $this->handleControllerInterface();
            $this->handleConstResourceNeed();
            $this->handleConstResourceMissed();
            $this->handleConstResourceValid();
        } catch (LogicException $e) {
            throw new LogicException(
                (new Message($e->getMessage()))
                    ->code('%interfaceController%', self::INTERFACE_CONTROLLER)
                    ->code('%reflectionName%', $this->reflection->getName())
                    // ->code('%interfaceControllerResource%', self::INTERFACE_CONTROLLER_RESOURCE)
                    ->code('%reflectionFilename%', $this->reflection->getFileName())
                    ->code('%endpoint%', $this->httpMethod . ' api/users')
                    ->code('%className%', $this->className)
                    ->code('%propResources%', 'const ' . self::PROP_RESOURCES)
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
                    (new Message('Class %className% implements %i interface, but it doesnt define any related resource'))
                        ->code('%className%', $className)
                        // ->code('%i', ControllerRelationshipInterface::class)
                        ->toString()
                );
            }
            $this->resources = $this->relatedResource::resources();
        }
    }

    private function handleControllerInterface(): void
    {
        if (!$this->reflection->implementsInterface(self::INTERFACE_CONTROLLER)) {
            throw new LogicException('Class %reflectionName% must implement the %interfaceController% interface at %reflectionFilename%');
        }
    }

    // private function handleControllerResourceInterface(): void
    // {
    //     if ($this->useResource && !$this->reflection->implementsInterface(self::INTERFACE_CONTROLLER_RESOURCE)) {
    //         throw new LogicException('Class %reflectionName% must implement the %interfaceControllerResource% interface at %reflectionFilename%.');
    //     }
    // }

    private function handleConstResourceNeed(): void
    {
        if (!empty($this->resources) && !$this->useResource) {
            throw new LogicException('Class %className% defines %propResources% but this Controller class targets a non-resourced endpoint: %endpoint%. Remove the unused %propResources% declaration at %filepath%');
        }
    }

    private function handleConstResourceMissed(): void
    {
        if (null == $this->resources && $this->isResource) {
            throw new LogicException('Class %className% must define %propResources% at %filepath%');
        }
    }

    private function handleConstResourceValid(): void
    {
        if (is_iterable($this->resources)) {
            foreach ($this->resources as $className) {
                if (!class_exists($className)) {
                    throw new LogicException(
                        (new Message('Class %s not found for %c Controller at %f'))
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
            // foreach ($this->resources as $propName => $resourceClassName) {
            // Better reflection is needed due to this: https://bugs.php.net/bug.php?id=69804
            // FIXME: Don't user BetterReflection as it adds PHPStorm stubs
            // $resourceReflection = (new BetterReflection())
            //     ->classReflector()
            //     ->reflect($resourceClassName);
            // if ($resourceReflection->implementsInterface(self::INTERFACE_CREATE_FROM_STRING)) {
            //     $resourcesFromString[$propName] = [
            //         'regex' => $resourceReflection->getStaticPropertyValue('stringRegex'),
            //         'description' => $resourceReflection->getStaticPropertyValue('stringDescription'),
            //     ];
            // }
            // }
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
        $classNamespace = (string) (new Str($className))->replaceLast('\\' . $classShortName, '');
        $classNamespaceNoApp = (string) (new Str($classNamespace))->replaceFirst('App\\', '');

        return (string) (new Str($classNamespaceNoApp))->forwardSlashes()->lowercase();
    }
}
