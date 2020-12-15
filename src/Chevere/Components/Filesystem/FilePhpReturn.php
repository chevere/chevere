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

namespace Chevere\Components\Filesystem;

use Chevere\Components\Message\Message;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Filesystem\FileHandleException;
use Chevere\Exceptions\Filesystem\FileInvalidContentsException;
use Chevere\Exceptions\Filesystem\FileNotExistsException;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Filesystem\FileUnableToGetException;
use Chevere\Exceptions\Filesystem\FileWithoutContentsException;
use Chevere\Exceptions\Serialize\UnserializeException;
use Chevere\Interfaces\Filesystem\FilePhpInterface;
use Chevere\Interfaces\Filesystem\FilePhpReturnInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Chevere\Interfaces\VarExportable\VarExportableInterface;
use Throwable;
use function Chevere\Components\Type\varType;

final class FilePhpReturn implements FilePhpReturnInterface
{
    private FilePhpInterface $filePhp;

    /** @var bool True for strict validation (self::PHP_RETURN_CHARS), false for regex validation (return <algo>) */
    private bool $strict = true;

    public function __construct(FilePhpInterface $filePhp)
    {
        $filePhp->file()->assertExists();
        $this->filePhp = $filePhp;
    }

    public function withStrict(bool $strict): FilePhpReturnInterface
    {
        $new = clone $this;
        $new->strict = $strict;

        return $new;
    }

    public function filePhp(): FilePhpInterface
    {
        return $this->filePhp;
    }

    /**
     *
     * @throws FileNotExistsException
     * @throws FileHandleException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     * @throws FileUnableToGetException
     * @throws RuntimeException
     */
    public function raw()
    {
        $this->assert();
        $filePath = $this->filePhp->file()->path()->absolute();
        // @codeCoverageIgnoreStart
        try {
            return include $filePath;
        } catch (Throwable $e) {
            throw new RuntimeException(
                (new Message('Thrown %message% when including %path%'))
                    ->code('%message%', $e->getMessage())
                    ->code('%path%', $filePath)
            );
        }
        // @codeCoverageIgnoreEnd
    }

    public function var()
    {
        $var = $this->raw();

        return $this->getReturnVar($var);
    }

    public function varType(TypeInterface $type)
    {
        $var = $this->var();
        if ($type->validate($var) === false) {
            $typeReturn = is_object($var) ? get_class($var) : varType($var);

            throw new FileReturnInvalidTypeException(
                (new Message("File PHP return of type %return% at %path% doesn't match the expected type %expected%"))
                    ->code('%return%', $typeReturn)
                    ->code('%path%', $this->filePhp->file()->path()->absolute())
                    ->code('%expected%', $type->typeHinting())
            );
        }

        return $var;
    }

    public function put(VarExportableInterface $varExportable): void
    {
        $var = $varExportable->var();
        $var = $this->getFileReturnVar($var);
        $varExport = var_export($var, true);
        $this->filePhp->file()->put(
            self::PHP_RETURN . $varExport . ';'
        );
    }

    private function getReturnVar($var)
    {
        if (is_string($var) && !ctype_space($var)) {
            try {
                $unserialize = new Unserialize($var);
                $var = $unserialize->var();
            } catch (UnserializeException $e) {
                // $e
            }
        }

        return $var;
    }

    private function assert(): void
    {
        if ($this->strict === true) {
            $this->assertStrict();

            return;
        }
        $this->assertNonStrict();
    }

    private function assertStrict(): void
    {
        $this->filePhp->file()->assertExists();
        $filename = $this->filePhp->file()->path()->absolute();
        $handle = fopen($filename, 'r');
        if (false === $handle) {
            // @codeCoverageIgnoreStart
            throw new FileHandleException(
                (new Message('Unable to open %path% in %mode% mode'))
                    ->code('%path%', $filename)
                    ->code('%mode%', 'r')
            );
            // @codeCoverageIgnoreEnd
        }
        $contents = fread($handle, self::PHP_RETURN_CHARS);
        fclose($handle);
        if ($contents === '') {
            throw new FileWithoutContentsException(
                (new Message("The file %path% doesn't have any contents"))
                    ->code('%path%', $filename)
            );
        }
        if (self::PHP_RETURN !== $contents) {
            throw new FileInvalidContentsException(
                (new Message('Unexpected contents in %path%, strict validation requires a file return in the form of %expected%'))
                    ->code('%path%', $filename)
                    ->code('%expected%', self::PHP_RETURN . '$var;')
            );
        }
    }

    /**
     * @throws FileNotExistsException
     * @throws FileUnableToGetException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     */
    private function assertNonStrict(): void
    {
        $contents = $this->filePhp->file()->contents();

        try {
            (new StrAssert($contents))->notEmpty()->notCtypeSpace();
        } catch (Throwable $e) {
            throw new FileWithoutContentsException(
                (new Message("The file at %path% doesn't have any contents (non-strict validation)"))
                    ->code('%path%', $this->filePhp->file()->path()->absolute())
            );
        }
        if (preg_match('#<?php[\S\s]*\s*return\s*[\S\s]*;#', $contents) !== 1) {
            throw new FileInvalidContentsException(
                (new Message('Unexpected contents in %path% (non-strict validation)'))
                    ->code('%path%', $this->filePhp->file()->path()->absolute())
            );
        }
    }

    private function getFileReturnVar($var)
    {
        if (is_object($var)) {
            return serialize($var);
        }

        return $var;
    }
}
