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
use function Chevere\Message\message;
use Chevere\Serialize\Deserialize;
use Chevere\Serialize\Serialize;
use Chevere\String\AssertString;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\VariableSupport\Interfaces\StorableVariableInterface;
use Throwable;

final class FilePhpReturn implements FilePhpReturnInterface
{
    public function __construct(
        private FilePhpInterface $filePhp
    ) {
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

    public function variable(): mixed
    {
        return $this->getReturnVariable($this->raw());
    }

    public function variableTyped(TypeInterface $type): mixed
    {
        if (!$type->validate($this->variable())) {
            $typeReturn = get_debug_type($this->variable());

            throw new FileReturnInvalidTypeException(
                message("File PHP return of type %return% at %path% doesn't match the expected type %expected%")
                    ->withCode('%return%', $typeReturn)
                    ->withCode('%path%', $this->filePhp->file()->path()->__toString())
                    ->withCode('%expected%', $type->typeHinting())
            );
        }

        return $this->variable();
    }

    public function put(StorableVariableInterface $storableVariable): void
    {
        $variable = $storableVariable->variable();
        $variable = $this->getFileReturnVariable($variable);
        $varExport = var_export($variable, true);
        $this->filePhp->file()->put(
            self::PHP_RETURN . $varExport . ';'
        );
    }

    private function getReturnVariable(mixed $variable): mixed
    {
        if (is_string($variable) && !ctype_space($variable)) {
            try {
                $unserialize = new Deserialize($variable);
                $variable = $unserialize->variable();
            } catch (InvalidArgumentException $e) {
                // $e
            }
        }

        return $variable;
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
            (new AssertString($contents))->notEmpty()->notCtypeSpace();
        } catch (Throwable) {
            throw new FileWithoutContentsException(
                message("The file at %path% doesn't have any contents")
                    ->withCode('%path%', $this->filePhp->file()->path()->__toString())
            );
        }
        if (preg_match('#^<\?php[\S\s]*return[\S\s]*;$#', $contents) !== 1) {
            throw new FileInvalidContentsException(
                message('Unexpected contents in %path%')
                    ->withCode('%path%', $this->filePhp->file()->path()->__toString())
            );
        }
    }

    private function getFileReturnVariable(mixed $variable): mixed
    {
        if (is_object($variable)) {
            return (new Serialize($variable))->__toString();
        }

        return $variable;
    }
}
