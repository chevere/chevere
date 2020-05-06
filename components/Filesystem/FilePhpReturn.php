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

use Chevere\Components\Filesystem\Exceptions\FileHandleException;
use Chevere\Components\Filesystem\Exceptions\FileInvalidContentsException;
use Chevere\Components\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Components\Filesystem\Exceptions\FileReturnInvalidTypeException;
use Chevere\Components\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Components\Filesystem\Exceptions\FileWithoutContentsException;
use Chevere\Components\Filesystem\Interfaces\FilePhpInterface;
use Chevere\Components\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Serialize\Exceptions\UnserializeException;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Components\Str\StrAssert;
use Chevere\Components\Type\Interfaces\TypeInterface;
use Chevere\Components\VarExportable\Interfaces\VarExportableInterface;
use Throwable;

/**
 * PhpFileReturn interacts with .php files that return a variable.
 *
 * <?php return 'Hello World!';
 */
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

    public function raw()
    {
        $this->validate();

        return include $this->filePhp->file()->path()->absolute();
    }

    public function var()
    {
        $var = $this->raw();
        $var = $this->getReturnVar($var);

        return $var;
    }

    public function varType(TypeInterface $type)
    {
        $var = $this->var();
        if ($type->validate($var) === false) {
            throw new FileReturnInvalidTypeException(
                (new Message("Return type %return% doesn't match the expected type %expected%"))
                    ->code('%return%', gettype($var))
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

    private function validate(): void
    {
        if ($this->strict === true) {
            $this->validateStrict();

            return;
        }
        $this->validateNonStrict();
    }

    private function validateStrict(): void
    {
        $this->filePhp->file()->assertExists();
        $filename = $this->filePhp->file()->path()->absolute();
        $handle = fopen($filename, 'r');
        if (false === $handle) {
            // @codeCoverageIgnoreStart
            throw new FileHandleException(
                (new Message('Unable to %fn% %path% in %mode% mode'))
                    ->code('%fn%', 'fopen')
                    ->code('%path%', $filename)
                    ->code('%mode%', 'r')
            );
            // @codeCoverageIgnoreEnd
        }
        $contents = fread($handle, self::PHP_RETURN_CHARS);
        fclose($handle);
        if ('' == $contents) {
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
    private function validateNonStrict(): void
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
        if (!preg_match_all('#<\?php([\S\s]*)\s*return\s*[\S\s]*;#', $contents)) {
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
