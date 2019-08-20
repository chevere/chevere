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
use Chevere\Type;
use Chevere\Path\PathHandle;

// FIXME: Make a client for injecting type. Don't pass type in construct pls.

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class ArrayFile implements IteratorAggregate, ArrayAccess
{
    /** @var array The array returned by the file */
    private $array;

    /** @var string The file containing return [array] */
    private $filepath;

    /** @var string A type, class name or interface that all array members must implement */
    private $typeMatch;

    /** @var string The primitive type for typeMatch */
    private $typePrimitive;

    /******** */

    /** @var Type */
    private $type;

    /**
     * @param string $fileHandle Path handle or absolute filepath
     * @param Type   $type       If set, the array members must match the target type, classname or interface
     */
    public function __construct(PathHandle $pathHandle, Type $type = null)
    {
        $filepath = $pathHandle->path();
        $this->array = include $filepath;
        $this->filepath = $filepath;
        $this->arrayFileType = gettype($this->array);
        try {
            $this->validateIsArray();
            if (null !== $type) {
                $this->type = $type;
                $this->validate();
            }
        } catch (LogicException $e) {
            throw new LogicException(
                (new Message($e->getMessage()))
                    ->code('%arrayFileType%', $this->arrayFileType)
                    ->code('%filepath%', $filepath)
                    ->code('%members%', $this->type->typeString())
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
        return $this->type->primitive() ?? null;
    }

    public function toArray(): array
    {
        return $this->array ?? [];
    }

    private function validateIsArray(): void
    {
        if (!is_array($this->array)) {
            throw new LogicException('Expecting file %filepath% return type array, %arrayFileType% provided.');
        }
    }

    /**
     * Validates array content type.
     */
    private function validate(): void
    {
        $validator = $this->type->validator();
        foreach ($this->array as $k => $object) {
            if ($validate = $validator($object)) {
                if ($this->type->primitive() == 'object') {
                    $validate = $this->type->validate($object);
                }
            }
            if (!$validate) {
                $this->handleInvalidation($k, $object);
            }
        }
    }

    private function handleInvalidation($k, $object): void
    {
        $type = gettype($object);
        if ($type == 'object') {
            $type .= ' ' . get_class($object);
        }
        throw new LogicException(
            (new Message('Expecting array containing only %members% members, type %type% found at %filepath% (array key %key%).'))
                ->code('%type%', $type)
                ->code('%key%', $k)
                ->toString()
        );
    }
}
