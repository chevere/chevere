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

use Chevere\Components\ArrayFile\Exceptions\ArrayFileTypeException;
use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileReturnInvalidTypeException;
use Chevere\Components\File\FileReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Chevere\Contracts\ArrayFile\ArrayFileContract;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FilePhpContract;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class ArrayFile implements ArrayFileContract
{
    /** @var array The array returned by the file */
    private $array;

    /** @var FilePhpContract */
    private $filePhp;

    /** @var Type */
    private $type;

    /**
     * {@inheritdoc}
     */
    public function __construct(FilePhpContract $filePhp)
    {
        $this->filePhp = $filePhp;
        $this->assertIsFile();
        $fileReturn = (new FileReturn($this->filePhp))
            ->withNoStrict();
        $this->array = $fileReturn->return();
        $this->validateReturnIsArray();
    }

    /**
     * {@inheritdoc}
     */
    public function withMembersType(Type $type): ArrayFileContract
    {
        $new = clone $this;
        $new->type = $type;
        $new->validateMembers();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function file(): FileContract
    {
        return $this->filePhp->file();
    }

    /**
     * {@inheritdoc}
     */
    public function array(): array
    {
        return $this->array;
    }

    private function assertIsFile(): void
    {
        if (!$this->filePhp->file()->exists()) {
            throw new FileNotFoundException(
                (new Message('File %path% not found'))
                    ->code('%path%', $this->filePhp->file()->path()->absolute())
                    ->toString()
            );
        }
    }

    private function validateReturnIsArray(): void
    {
        $type = gettype($this->array);
        if ('array' !== $type) {
            throw new FileReturnInvalidTypeException(
                (new Message('Expecting file %path% return type array, %returnType% provided'))
                    ->code('%path%', $this->filePhp->file()->path()->absolute())
                    ->code('%returnType%', $type)
                    ->toString()
            );
        }
    }

    private function validateMembers(): void
    {
        $validator = $this->type->validator();
        foreach ($this->array as $k => $object) {
            $validate = $validator($object);
            if ($validate) {
                if ('object' == $this->type->primitive()) {
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
        if ('object' == $type) {
            $type .= ' ' . get_class($object);
        }
        throw new ArrayFileTypeException(
            (new Message('Expecting array containing only %members% members, type %type% found at %filepath% (key %key%)'))
                ->code('%members%', $this->type->typeString())
                ->code('%filepath%', $this->filePhp->file()->path()->absolute())
                ->code('%type%', $type)
                ->code('%key%', $k)
                ->toString()
        );
    }
}
