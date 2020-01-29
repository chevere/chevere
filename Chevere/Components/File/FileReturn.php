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

namespace Chevere\Components\File;

use Chevere\Components\File\Exceptions\FileHandleException;
use Chevere\Components\File\Exceptions\FileInvalidContentsException;
use Chevere\Components\File\Exceptions\FileWithoutContentsException;
use Chevere\Components\Serialize\Exceptions\UnserializeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Components\File\Interfaces\FilePhpInterface;
use Chevere\Components\File\Interfaces\FileReturnInterface;
use Chevere\Components\Variable\Interfaces\VariableExportInterface;

/**
 * FileReturn interacts with PHP files that return something.
 *
 * <?php return 'Hello World!';
 */
final class FileReturn implements FileReturnInterface
{
    private FilePhpInterface $filePhp;

    /** @var bool True for strict validation (PHP_RETURN), false for regex validation (return <algo>) */
    private bool $strict;

    /**
     * Creates a new instance.
     *
     * @throws FileNotFoundException if the file doesn't exists
     */
    public function __construct(FilePhpInterface $filePhp)
    {
        $this->strict = true;
        $this->filePhp = $filePhp;
        $this->filePhp()->file()->assertExists();
    }

    /**
     * {@inheritdoc}
     */
    public function withNoStrict(): FileReturnInterface
    {
        $new = clone $this;
        $new->strict = false;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function filePhp(): FilePhpInterface
    {
        return $this->filePhp;
    }

    /**
     * {@inheritdoc}
     */
    public function raw()
    {
        $this->validate();

        return include $this->filePhp()->file()->path()->absolute();
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        $var = $this->raw();

        if (is_array($var)) {
            foreach ($var as &$v) {
                $v = $this->getReturnVar($v);
            }
        } else {
            $var = $this->getReturnVar($var);
        }

        return $var;
    }

    /**
     * {@inheritdoc}
     */
    public function put(VariableExportInterface $variableExport): void
    {
        $var = $variableExport->var();
        if (is_array($var)) {
            foreach ($var as &$v) {
                $v = $this->getFileReturnVar($v);
            }
        } else {
            $var = $this->getFileReturnVar($var);
        }
        $varExport = var_export($var, true);
        $this->filePhp()->file()->put(
            FileReturnInterface::PHP_RETURN . $varExport . ';'
        );
    }

    private function getReturnVar($var)
    {
        if (is_string($var)) {
            try {
                $unserialize = new Unserialize($var);
                $var = $unserialize->var();
            } catch (UnserializeException $e) {
                // $e control
            }
        }

        return $var;
    }

    private function validate(): void
    {
        if ($this->strict) {
            $this->validateStrict();

            return;
        }
        $this->validateNonStrict();
    }

    private function validateStrict(): void
    {
        $this->filePhp()->file()->assertExists();
        $filename = $this->filePhp()->file()->path()->absolute();
        $handle = fopen($filename, 'r');
        if (false === $handle) {
            // @codeCoverageIgnoreStart
            throw new FileHandleException(
                (new Message('Unable to %fn% %path% in %mode% mode'))
                    ->code('%fn%', 'fopen')
                    ->code('%path%', $filename)
                    ->code('%mode%', 'r')
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
        $contents = fread($handle, FileReturnInterface::PHP_RETURN_CHARS);
        fclose($handle);
        if ('' == $contents) {
            throw new FileWithoutContentsException(
                (new Message("The file %path% doesn't have any contents"))
                    ->code('%path%', $filename)
                    ->toString()
            );
        }
        if (FileReturnInterface::PHP_RETURN !== $contents) {
            throw new FileInvalidContentsException(
                (new Message('Unexpected contents in %path%, strict validation requires a file return in the form of %expected%'))
                    ->code('%path%', $filename)
                    ->code('%expected%', FileReturnInterface::PHP_RETURN . '$theVar;')
                    ->toString()
            );
        }
    }

    private function validateNonStrict(): void
    {
        $contents = $this->filePhp()->file()->contents();
        if (!$contents) {
            throw new FileWithoutContentsException(
                (new Message('Unable to get file %path% contents'))
                    ->code('%path%', $this->filePhp()->file()->path()->absolute())
                    ->toString()
            );
        }
        if (!preg_match_all('#<\?php([\S\s]*)\s*return\s*[\S\s]*;#', $contents)) {
            throw new FileInvalidContentsException(
                (new Message('Unexpected contents in %path% (non-strict validation)'))
                    ->code('%path%', $this->filePhp()->file()->path()->absolute())
                    ->toString()
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
