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

namespace Chevereto\Core\Traits;

use Chevereto\Core\Message;
use Chevereto\Core\Path;
use Chevereto\Core\Utils\Str;
use LogicException;
use ReflectionMethod;

trait ContainerTrait
{
    /** @var bool True if the instance contains static::OBJECTS */
    private $_objects;

    /** @var array An array containing propName => className|null */
    // protected $objects;

    /**
     * Retrieves the $objects property.
     */
    public function hasObjectsDefinition(): bool
    {
        $this->_detectObjectsDefinition();

        return $this->_objects;
    }

    /**
     * Provides ::hasAlgo. "Algo" refers to a property ($algo) in the object.
     */
    final public function __call(string $name, array $arguments = null)
    {
        if (method_exists($this, $name)) {
            $reflection = new ReflectionMethod($this, $name);
            $caller = debug_backtrace(0, 1)[0];
            $caller['file'] = Path::relative(Path::normalize($caller['file']));
            throw new LogicException(
                (string)
                    (new Message('Call to %p method %s in %f.'))
                        ->code('%p', $reflection->isPrivate() ? 'private' : 'protected')
                        ->code('%s', __CLASS__.'::'.$name)
                        ->code('%f', $caller['file'].':'.$caller['line'])
            );
        }
        $this->_detectObjectsDefinition();
        $prefix = substr($name, 0, 3); // Covers get,set,has
        $propertyName = lcfirst(Str::replaceFirst($prefix, null, $name));
        if (in_array($prefix, ['has', 'get'])) {
            $hasAlgo = $this->_callHasAlgo($propertyName);
            if ('has' === $prefix) {
                return $hasAlgo;
            }
            if ($hasAlgo) {
                return $this->{$propertyName};
            }
        }
    }

    private function _detectObjectsDefinition(): void
    {
        if (!isset($this->_objects)) {
            $this->_objects = defined(__CLASS__.'::OBJECTS');
        }
    }

    /**
     * The ::hasAlgo magic check properties and the object type (if any).
     */
    private function _callHasAlgo(string $propertyName): bool
    {
        $property = $this->{$propertyName};
        if (!isset($property)) {
            return false;
        }
        if ($this->_objects) {
            $acceptedClass = static::OBJECTS[$propertyName] ?? null;
            if (isset($acceptedClass)) {
                return $property instanceof $acceptedClass;
            }
        }

        return true;
    }
}
