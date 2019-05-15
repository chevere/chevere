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
use Roave\BetterReflection\BetterReflection;

abstract class ControllerInspectAbstract
{
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
                        ->code('%r', 'const ' . static::PROP_RESOURCES)
                        ->code('%e', $this->httpMethod . ' api/users')
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
                        ->code('%r', 'const ' . static::PROP_RESOURCES)
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
                        ->code('%r', 'const ' . static::PROP_RESOURCES)
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
        $classNs = Utils\Str::replaceLast('\\' . $this->reflection->getShortName(), null, $this->className);
        /** @var string The class namespace without App (Api\Users) */
        $classNsNoApp = Utils\Str::replaceFirst(APP_NS_HANDLE, null, $classNs);
        /** @var string The class namespace without App, lowercased with forward slashes (api/users) */
        $pathComponent = strtolower(Utils\Str::forwardSlashes($classNsNoApp));
        /** @var array Exploded / $pathComponent */
        $pathComponents = explode('/', $pathComponent);
        if ($this->useResource) {
            /** @var string The Controller resource wildcard path component {resource} */
            $resourceWildcard = '{' . array_keys($this->resources)[0] . '}';
            if ($this->isResource) {
                // Append the resource wildcard: api/users/{wildcard}
                $pathComponent .= '/' . $resourceWildcard;
            } elseif ($this->isRelatedResource) {
                /** @var string Pop the last path component, in this case a related resource */
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
