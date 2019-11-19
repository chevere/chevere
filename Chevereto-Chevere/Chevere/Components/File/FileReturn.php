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

namespace Chevere\Components\File;

use Chevere\Components\File\Exceptions\FileHandleException;
use Chevere\Components\File\Exceptions\FileInvalidContentsException;
use Chevere\Components\File\Exceptions\FileWithoutContentsException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FilePhpContract;
use Chevere\Contracts\File\FileReturnContract;

/**
 * FileReturn interacts with PHP files that return something.
 *
 * <?php return 'Hello World!';
 */
final class FileReturn implements FileReturnContract
{
    /** @var FilePhpContract */
    private $filePhp;

    /** @var bool True for strict validation (PHP_RETURN), false for regex validation (return <algo>) */
    private $strict;

    /**
     * {@inheritdoc}
     */
    public function __construct(FilePhpContract $filePhp)
    {
        $this->strict = true;
        $this->filePhp = $filePhp;
        $this->file()->assertExists();
    }

    /**
     * {@inheritdoc}
     */
    public function withNoStrict(): FileReturnContract
    {
        $new = clone $this;
        $new->strict = false;

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
    public function return()
    {
        $this->validate();

        return include $this->file()->path()->absolute();
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        $var = $this->return();

        if (is_iterable($var)) {
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
    public function put($var): void
    {
        if (is_iterable($var)) {
            foreach ($var as &$v) {
                $v = $this->getFileReturnVar($v);
            }
        } else {
            $var = $this->getFileReturnVar($var);
        }

        $varExport = var_export($var, true);
        $this->file()->put(
            FileReturnContract::PHP_RETURN . $varExport . ';'
        );
    }

    private function assertExportable($var): void
    {
    }

    private function getReturnVar($var)
    {
        if (is_string($var)) {
            $aux = @unserialize($var);
            if (false !== $aux) {
                $var = $aux;
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
        $this->file()->assertExists();
        $handle = fopen($this->file()->path()->absolute(), 'r');
        if (false === $handle) {
            throw new FileHandleException(
                (new Message('Unable to %fn% %path% in %mode% mode'))
                    ->code('%fn%', 'fopen')
                    ->code('%path%', $this->file()->path()->absolute())
                    ->code('%mode%', 'r')
                    ->toString()
            );
        }
        $contents = fread($handle, FileReturnContract::PHP_RETURN_CHARS);
        fclose($handle);
        if ('' == $contents) {
            throw new FileWithoutContentsException(
                (new Message("The file %path% doesn't have any contents"))
                    ->code('%path%', $this->file()->path()->absolute())
                    ->toString()
            );
        }
        if (FileReturnContract::PHP_RETURN !== $contents) {
            throw new FileInvalidContentsException(
                (new Message('Unexpected contents in %path% (strict validation)'))
                    ->code('%path%', $this->file()->path()->absolute())
                    ->toString()
            );
        }
    }

    private function validateNonStrict(): void
    {
        $contents = $this->file()->contents();
        if (!$contents) {
            throw new FileWithoutContentsException(
                (new Message('Unable to get file %path% contents'))
                    ->code('%path%', $this->file()->path()->absolute())
                    ->toString()
            );
        }
        if (!preg_match_all('#<\?php([\S\s]*)\s*return\s*[\S\s]*;#', $contents)) {
            throw new FileInvalidContentsException(
                (new Message('Unexpected contents in %path% (non-strict validation)'))
                    ->code('%path%', $this->file()->path()->absolute())
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
