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
use Chevere\Components\Filesystem\Exceptions\File\FileReturnInvalidTypeException;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\ArrayFile\Interfaces\ArrayFileInterface;
use Chevere\Components\Filesystem\Interfaces\File\FileInterface;
use Chevere\Components\Filesystem\Interfaces\File\PhpFileInterface;
use Chevere\Components\Type\Interfaces\TypeInterface;

/**
 * ArrayFile provides a object oriented method to interact with array files (return []).
 */
final class ArrayFile implements ArrayFileInterface
{
    /** @var array The array returned by the file */
    private array $array;

    private PhpFileInterface $filePhp;

    private TypeInterface $type;

    /**
     * Creates a new instance.
     *
     * @throws FileNotFoundException          if the $filePhp doesn't exists
     * @throws FileReturnInvalidTypeException if the actual file return isn't type array
     */
    public function __construct(PhpFileInterface $filePhp)
    {
        $this->filePhp = $filePhp;
        $this->filePhp->file()->assertExists();
        $fileReturn = (new PhpFileReturn($this->filePhp))
            ->withStrict(false);
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

    public function withMembersType(TypeInterface $type): ArrayFileInterface
    {
        $new = clone $this;
        $new->type = $type;
        foreach ($new->array as $pos => $val) {
            $validate = (bool) $new->type->validator()($val);
            if ($validate) {
                $validate = $new->type->validate($val);
            }
            if (!$validate) {
                $new->handleValidation($pos, $val);
            }
        }

        // *false+ @ php 7.4.2 xDebug 2.9.1 phpUnit 8.5.2*
        return $new; // @codeCoverageIgnore
    }

    public function file(): FileInterface
    {
        return $this->filePhp->file();
    }

    public function array(): array
    {
        return $this->array;
    }

    private function handleValidation($pos, $val): void
    {
        $type = gettype($val);
        if ('object' == $type) {
            $type .= ' ' . get_class($val);
        }
        throw new ArrayFileTypeException(
            (new Message('Expecting array containing only %membersType% members, type %type% found at %filepath% (pos %pos%)'))
                ->code('%membersType%', $this->type->typeHinting())
                ->code('%filepath%', $this->filePhp->file()->path()->absolute())
                ->code('%type%', $type)
                ->code('%pos%', (string) $pos)
                ->toString()
        );
    }
}
