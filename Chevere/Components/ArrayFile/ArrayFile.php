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

namespace Chevere\Components\ArrayFile;

use TypeError;
use Chevere\Components\ArrayFile\Exceptions\ArrayFileTypeException;
use Chevere\Components\File\Exceptions\FileReturnInvalidTypeException;
use Chevere\Components\File\FileReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\ArrayFile\Interfaces\ArrayFileInterface;
use Chevere\Components\File\Interfaces\FileInterface;
use Chevere\Components\File\Interfaces\FilePhpInterface;
use Chevere\Components\Type\Interfaces\TypeInterface;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class ArrayFile implements ArrayFileInterface
{
    /** @var array The array returned by the file */
    private array $array;

    private FilePhpInterface $filePhp;

    private TypeInterface $type;

    /**
     * Creates a new instance.
     *
     * @throws FileNotFoundException          if the $filePhp doesn't exists
     * @throws FileReturnInvalidTypeException if the actual file return isn't type array
     */
    public function __construct(FilePhpInterface $filePhp)
    {
        $this->filePhp = $filePhp;
        $this->filePhp->file()->assertExists();
        $fileReturn = (new FileReturn($this->filePhp))
            ->withNoStrict();
        try {
            $raw = $fileReturn->raw();
            $this->array = $fileReturn->raw();
        } catch (TypeError $e) {
            throw new FileReturnInvalidTypeException(
                (new Message('Return value of file %path% must be type array, %returnType% provided'))
                    ->code('%path%', $this->filePhp->file()->path()->absolute())
                    ->code('%returnType%', gettype($raw))
                    ->toString()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function withMembersType(TypeInterface $type): ArrayFileInterface
    {
        $new = clone $this;
        $new->type = $type;
        $new->validateMembers();

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function file(): FileInterface
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

    private function validateMembers(): void
    {
        $validator = $this->type->validator();
        foreach ($this->array as $k => $val) {
            $validate = $validator($val);
            if ($validate) {
                $validate = $this->type->validate($val);
            }
            if (!$validate) {
                $this->handleInvalidation($k, $val);
            }
        }
    }

    private function handleInvalidation($k, $val): void
    {
        $type = gettype($val);
        if ('object' == $type) {
            $type .= ' ' . get_class($val);
        }
        throw new ArrayFileTypeException(
            (new Message('Expecting array containing only %membersType% members, type %type% found at %filepath% (key %key%)'))
                ->code('%membersType%', $this->type->typeHinting())
                ->code('%filepath%', $this->filePhp->file()->path()->absolute())
                ->code('%type%', $type)
                ->code('%key%', $k)
                ->toString()
        );
    }
}