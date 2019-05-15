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

/**
 * Provides information about any Controller implementing the Interfaces\ControllerInterface interface.
 */
class ControllerInspect extends ControllerInspectAbstract implements Interfaces\ToArrayInterface
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
}
