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

use RuntimeException;

use Chevere\Components\Message\Message;
use Chevere\Contracts\File\FileContract;
use Chevere\Contracts\File\FilePhpContract;
use Chevere\Contracts\File\FileReturnContract;

/**
 * FileReturn interacts with PHP files that return something.
 *
 * <?php return 'Hello World!';
 * 
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
    public function checksum(): string
    {
        return hash_file(FileReturnContract::CHECKSUM_ALGO, $this->file()->path()->absolute());
    }

    /**
     * {@inheritdoc}
     */
    public function contents(): string
    {
        $contents = file_get_contents($this->file()->path()->absolute());
        if (false === $contents) {
            throw new RuntimeException(
                (new Message('Unable to read the contents of the file at %path%'))
                    ->code('%path%', $this->file()->path()->absolute())
                    ->toString()
            );
        }

        return $contents;
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
    public function get()
    {
        $var = $this->return();
        if (is_iterable($var)) {
            foreach ($var as &$v) {
                $v = unserialize($v);
            }
        } else {
            $var = unserialize($v);
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
                $this->switchVar($v);
            }
        } else {
            $this->switchVar($var);
        }
        $varExport = var_export($var, true);
        $this->file()->put(
            FileReturnContract::PHP_RETURN . $varExport . ';'
        );
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
        $handle = fopen($this->file()->path()->absolute(), 'r');
        if (false === $handle) {
            throw new RuntimeException(
                (new Message('Unable to %fn% %path% in %mode% mode'))
                    ->code('%fn%', 'fopen')
                    ->code('%path%', $this->file()->path()->absolute())
                    ->code('%mode%', 'r')
                    ->toString()
            );
        }
        $contents = fread($handle, FileReturnContract::PHP_RETURN_CHARS);
        fclose($handle);
        if ($contents !== FileReturnContract::PHP_RETURN) {
            throw new RuntimeException(
                (new Message('Unexpected contents in %path% (strict validation)'))
                    ->code('%path%', $this->file()->path()->absolute())
                    ->toString()
            );
        }
    }

    private function validateNonStrict(): void
    {
        $contents = $this->contents();
        if (!$contents) {
            throw new RuntimeException(
                (new Message('Unable to get file %path% contents'))
                    ->code('%path%', $this->file()->path()->absolute())
                    ->toString()
            );
        }
        if (!preg_match_all('#<\?php([\S\s]*)\s*return\s*[\S\s]*;#', $contents)) {
            throw new RuntimeException(
                (new Message('Unexpected contents in %path% (non-strict validation)'))
                    ->code('%path%', $this->file()->path()->absolute())
                    ->toString()
            );
        }
    }

    private function switchVar(&$var)
    {
        if (is_object($var)) {
            $var = serialize($var);
        }
    }
}
