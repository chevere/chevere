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

namespace Chevere\ArrayFile;

use ArrayAccess;
use ArrayIterator;
use LogicException;
use IteratorAggregate;
use Chevere\Message;
use Chevere\Path\PathHandle;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class ArrayFile implements IteratorAggregate, ArrayAccess
{
    /** @const array Type validators, taken from https://www.php.net/manual/en/ref.var.php */
    const TYPE_VALIDATORS = [
        'array' => 'is_array',
        'bool' => 'is_bool',
        'callable' => 'is_callable',
        'countable' => 'is_countable',
        'double' => 'is_double',
        'float' => 'is_float',
        'int' => 'is_int',
        'integer' => 'is_integer',
        'iterable' => 'is_iterable',
        'long' => 'is_long',
        'null' => 'is_null',
        'numeric' => 'is_numeric',
        'object' => 'is_object',
        'real' => 'is_real',
        'resource' => 'is_resource',
        'scalar' => 'is_scalar',
        'string' => 'is_string',
    ];

    /** @var array */
    private $array;

    /** @var string */
    private $filepath;

    /** @var string A type, classname or interface */
    private $typeSome;

    /** @var string */
    private $type;

    /** @var string */
    private $className;

    /** @var string */
    private $interfaceName;

    /**
     * @param string $fileHandle Path handle or absolute filepath
     * @param array  $typeSome   If set, the array members must match the target type, classname or interface
     */
    public function __construct(PathHandle $pathHandle, string $typeSome = null)
    {
        $filepath = $pathHandle->path();
        $this->typeSome = $typeSome;
        $fileArray = include $filepath;
        $this->filepath = $filepath;
        $arrayFileType = gettype($fileArray);
        try {
            $this->handleFileArray($fileArray);
            if (null !== $typeSome) {
                $this->handleTypeSome($this->typeSome);
                $this->handleNullType($this->type);
                $this->validate($fileArray);
            }
        } catch (LogicException $e) {
            throw new LogicException(
                (new Message($e->getMessage()))
                    ->code('%arrayFileType%', $arrayFileType)
                    ->code('%filepath%', $filepath)
                    ->code('%members%', $this->className ?? $this->interfaceName ?? $this->type)
                    ->code('%typeSome%', $typeSome)
                    ->toString()
            );
        }
        $this->array = $fileArray;
    }

    public function offsetSet($offset, $value)
    {
        $this->array[$offset ?? ''] = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->array[$offset] ?? null;
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->array);
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getType(): ?string
    {
        return $this->type ?? null;
    }

    public function toArray(): array
    {
        return $this->array ?? [];
    }

    private function handleFileArray($fileArray)
    {
        if (!is_array($fileArray)) {
            throw new LogicException('Expecting file %filepath% return type array, %arrayFileType% provided.');
        }
    }

    private function handleTypeSome($typeSome)
    {
        if (isset(static::TYPE_VALIDATORS[$typeSome])) {
            $this->type = $typeSome;
        } else {
            $this->handleClassAndInterfaceName($typeSome);
            if (null != $this->className || null != $this->interfaceName) {
                $this->type = 'object';
            }
        }
    }

    private function handleClassAndInterfaceName(string $typeSome)
    {
        if (class_exists($typeSome)) {
            $this->className = $typeSome;
        } elseif (interface_exists($typeSome)) {
            $this->interfaceName = $typeSome;
        }
    }

    private function handleNullType($type)
    {
        if (null == $type) {
            throw new LogicException('Argument #2 must be a valid data type, class name or interface name. %typeSome% provided.');
        }
    }

    /**
     * Validates array content type.
     */
    private function validate(array $array): self
    {
        $validator = static::TYPE_VALIDATORS[$this->type];
        foreach ($array as $k => $v) {
            if ($validate = $validator($v)) {
                if ($this->type == 'object') {
                    $validate = $this->getValidateObject($v);
                }
            }
            if (false == $validate) {
                $this->handleInvalidation($k, $v);
            }
        }

        return $this;
    }

    private function getValidateObject(object $object): bool
    {
        if (isset($this->className)) {
            return get_class($object) == $this->className;
        } elseif (isset($this->interfaceName)) {
            return $object instanceof $this->interfaceName;
        }

        return false;
    }

    private function handleInvalidation($k, $v)
    {
        $type = gettype($v);
        if ($type == 'object') {
            $type .= ' '.get_class($v);
        }
        throw new LogicException(
            (new Message('Expecting array containing only %members% members, type %type% found at %filepath% (array key %key%).'))
                ->code('%type%', $type)
                ->code('%key%', $k)
                ->toString()
        );
    }
}
