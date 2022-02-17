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

namespace Chevere\Filesystem;

use Chevere\Filesystem\Exceptions\FileHandleException;
use Chevere\Filesystem\Exceptions\FileInvalidContentsException;
use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileReturnInvalidTypeException;
use Chevere\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Filesystem\Exceptions\FileWithoutContentsException;
use Chevere\Filesystem\Interfaces\FilePhpInterface;
use Chevere\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\Message\Message;
use Chevere\Serialize\Deserialize;
use Chevere\Serialize\Serialize;
use Chevere\Str\StrAssert;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VarSupport\Interfaces\VarStorableInterface;
use Throwable;

final class FilePhpReturn implements FilePhpReturnInterface
{
    public function __construct(
        private FilePhpInterface $filePhp
    ) {
        $this->filePhp->file()->assertExists();
    }

    public function filePhp(): FilePhpInterface
    {
        return $this->filePhp;
    }

    /**
     * @throws FileNotExistsException
     * @throws FileHandleException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     * @throws FileUnableToGetException
     * @throws RuntimeException
     */
    public function raw(): mixed
    {
        $this->assert();
        $filePath = $this->filePhp->file()->path()->__toString();
        // @codeCoverageIgnoreStart
        // @infection-ignore-all
        try {
            return include $filePath;
        } catch (Throwable $e) {
            throw new RuntimeException(previous: $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function var(): mixed
    {
        return $this->getReturnVar($this->raw());
    }

    public function varType(TypeInterface $type): mixed
    {
        if (!$type->validate($this->var())) {
            $typeReturn = get_debug_type($this->var());

            throw new FileReturnInvalidTypeException(
                (new Message("File PHP return of type %return% at %path% doesn't match the expected type %expected%"))
                    ->code('%return%', $typeReturn)
                    ->code('%path%', $this->filePhp->file()->path()->__toString())
                    ->code('%expected%', $type->typeHinting())
            );
        }

        return $this->var();
    }

    public function put(VarStorableInterface $varStorable): void
    {
        $var = $varStorable->var();
        $var = $this->getFileReturnVar($var);
        $varExport = var_export($var, true);
        $this->filePhp->file()->put(
            self::PHP_RETURN . $varExport . ';'
        );
    }

    private function getReturnVar($var): mixed
    {
        if (is_string($var) && !ctype_space($var)) {
            try {
                $unserialize = new Deserialize($var);
                $var = $unserialize->var();
            } catch (InvalidArgumentException $e) {
                // $e
            }
        }

        return $var;
    }

    /**
     * @throws FileNotExistsException
     * @throws FileUnableToGetException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     */
    private function assert(): void
    {
        $contents = $this->filePhp->file()->getContents();

        try {
            (new StrAssert($contents))->notEmpty()->notCtypeSpace();
        } catch (Throwable $e) {
            throw new FileWithoutContentsException(
                (new Message("The file at %path% doesn't have any contents"))
                    ->code('%path%', $this->filePhp->file()->path()->__toString())
            );
        }
        if (preg_match('#<?php[\S\s]*\s*return\s*[\S\s]*;#', $contents) !== 1) {
            throw new FileInvalidContentsException(
                (new Message('Unexpected contents in %path%'))
                    ->code('%path%', $this->filePhp->file()->path()->__toString())
            );
        }
    }

    private function getFileReturnVar($var): mixed
    {
        if (is_object($var)) {
            return (new Serialize($var))->__toString();
        }

        return $var;
    }
}
