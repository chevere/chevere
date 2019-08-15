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
    /** @const array Type validators [primitive => validator], taken from https://www.php.net/manual/en/ref.var.php */
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

    /** @var array The array returned by the file */
    private $array;

    /** @var string The file containing return [array] */
    private $filepath;

    /** @var string A type, class name or interface that all array members must implement */
    private $typeMatch;

    /** @var string The primitive type for typeMatch */
    private $typePrimitive;

    /** @var string */
    private $typeMatchClassName;

    /** @var string */
    private $typeMatchInterfaceName;

    /**
     * @param string $fileHandle Path handle or absolute filepath
     * @param array  $typeMatch  If set, the array members must match the target type, classname or interface
     */
    public function __construct(PathHandle $pathHandle, string $typeMatch = null)
    {
        $filepath = $pathHandle->path();
        $this->typeMatch = $typeMatch;
        $this->array = include $filepath;
        $this->filepath = $filepath;
        $arrayFileType = gettype($this->array);
        try {
            $this->validateArrayType();
            if (null !== $typeMatch) {
                $this->handleTypeSome();
                $this->validateNotNullType();
                $this->validate();
            }
        } catch (LogicException $e) {
            throw new LogicException(
                (new Message($e->getMessage()))
                    ->code('%arrayFileType%', $arrayFileType)
                    ->code('%filepath%', $filepath)
                    ->code('%members%', $this->typeMatchClassName ?? $this->typeMatchInterfaceName ?? $this->typePrimitive)
                    ->code('%typeMatch%', $typeMatch)
                    ->toString()
            );
        }
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
        return $this->typePrimitive ?? null;
    }

    public function toArray(): array
    {
        return $this->array ?? [];
    }

    private function validateArrayType(): void
    {
        if (!is_array($this->array)) {
            throw new LogicException('Expecting file %filepath% return type array, %arrayFileType% provided.');
        }
    }

    private function handleTypeSome(): void
    {
        if (isset(static::TYPE_VALIDATORS[$this->typeMatch])) {
            $this->typePrimitive = $this->typeMatch;
        } else {
            $this->handleClassAndInterfaceName();
            if (null != $this->typeMatchClassName || null != $this->typeMatchInterfaceName) {
                $this->typePrimitive = 'object';
            }
        }
    }

    private function handleClassAndInterfaceName(): void
    {
        if (class_exists($this->typeMatch)) {
            $this->typeMatchClassName = $this->typeMatch;
        } elseif (interface_exists($this->typeMatch)) {
            $this->typeMatchInterfaceName = $this->typeMatch;
        }
    }

    private function validateNotNullType(): void
    {
        if (null == $this->typePrimitive) {
            throw new LogicException('Argument #2 must be a valid data type, class name or interface name. %typeMatch% provided.');
        }
    }

    /**
     * Validates array content type.
     */
    private function validate(): void
    {
        $validator = static::TYPE_VALIDATORS[$this->typePrimitive];
        foreach ($this->array as $k => $v) {
            if ($validate = $validator($v)) {
                if ($this->typePrimitive == 'object') {
                    $validate = $this->getValidateObject($v);
                }
            }
            if (false == $validate) {
                $this->handleInvalidation($k, $v);
            }
        }
    }

    private function getValidateObject(object $object): bool
    {
        if (isset($this->typeMatchClassName)) {
            return get_class($object) == $this->typeMatchClassName;
        } elseif (isset($this->typeMatchInterfaceName)) {
            return $object instanceof $this->typeMatchInterfaceName;
        }

        return false;
    }

    private function handleInvalidation($k, $v): void
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
