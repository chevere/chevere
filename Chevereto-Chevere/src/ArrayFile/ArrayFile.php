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
use IteratorAggregate;
use LogicException;
use Chevere\FileReturn\FileReturn;
use Chevere\Message\Message;
use Chevere\Type;
use Chevere\Path\PathHandle;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class ArrayFile implements IteratorAggregate, ArrayAccess
{
    /** @var array The array returned by the file */
    private $array;

    /** @var PathHandle */
    private $pathHandle;

    /** @var FileReturn */
    private $fileReturn;

    /** @var Type */
    private $type;

    /**
     * @param PathHandle $pathHandle Path handle or absolute filepath
     */
    public function __construct(PathHandle $pathHandle)
    {
        $this->pathHandle = $pathHandle;
        $this->fileReturn = new FileReturn($pathHandle);
        $this->fileReturn->setStrict(false);
        $this->validateIsArray();
        $this->array = $this->fileReturn->raw();
    }

    /**
     * @param Type $type The array members must match the target type, classname or interface.
     */
    public function setMembersType(Type $type): void
    {
        $this->type = $type;
        $this->validate();
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

    public function pathHandle(): PathHandle
    {
        return $this->pathHandle;
    }

    public function toArray(): array
    {
        return $this->array ?? [];
    }

    private function validateIsArray(): void
    {
        if ('array' !== $this->fileReturn->type()) {
            throw new LogicException(
                (new Message('Expecting file %filepath% return type array, %returnType% provided.'))
                    ->code('%filepath%', $this->pathHandle->path())
                    ->code('%returnType%', $this->fileReturn->type())
            );
        }
    }

    /**
     * Validate array members type.
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
                ->code('%members%', $this->type->typeString())
                ->code('%filepath%', $this->pathHandle->path())
                ->code('%type%', $type)
                ->code('%key%', $k)
                ->toString()
        );
    }
}
