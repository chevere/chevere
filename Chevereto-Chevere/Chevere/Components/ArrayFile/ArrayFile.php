<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\ArrayFile;

use ArrayAccess;
use ArrayIterator;
use Chevere\Components\File\File;
use IteratorAggregate;
use LogicException;

use Chevere\Components\FileReturn\FileReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Type\Type;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class ArrayFile
{
    /** @var array The array returned by the file */
    private $array;

    /** @var Path */
    private $path;

    /** @var FileReturn */
    private $fileReturn;

    /** @var Type */
    private $type;

    public function __construct(Path $path)
    {
        $file = new File($path);
        $this->path = $path;
        $this->fileReturn = (new FileReturn($file))
            ->withNoStrict();
        $this->validateIsArray();
        $this->array = $this->fileReturn->raw();
    }

    /**
     * @param Type $type The array members must match the target type, classname or interface.
     */
    public function withMembersType(Type $type): ArrayFile
    {
        $new = clone $this;
        $new->type = $type;
        $new->validate();

        return $new;
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function toArray(): array
    {
        return $this->array ?? [];
    }

    private function validateIsArray(): void
    {
        if ('array' !== $this->fileReturn->type()) {
            throw new LogicException(
                (new Message('Expecting file %filepath% return type array, %returnType% provided'))
                    ->code('%filepath%', $this->path->absolute())
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
            $validate = $validator($object);
            if ($validate) {
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
            (new Message('Expecting array containing only %members% members, type %type% found at %filepath% (array key %key%)'))
                ->code('%members%', $this->type->typeString())
                ->code('%filepath%', $this->path->absolute())
                ->code('%type%', $type)
                ->code('%key%', $k)
                ->toString()
        );
    }
}
