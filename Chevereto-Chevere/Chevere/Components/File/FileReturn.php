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

use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileNotPhpException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\File\FileContract;

/**
 * FileReturn interacts with PHP files that return something.
 *
 * <?php return 'Hello World!';
 * 
 * This class allows to  
 */
final class FileReturn
{
    const PHP_RETURN = "<?php\n\nreturn ";
    const PHP_RETURN_CHARS = 14;
    const CHECKSUM_ALGO = 'sha256';

    /** @var FileContract */
    private $file;

    /** @var bool True for strict validation (PHP_RETURN), false for regex validation (return <algo>) */
    private $strict;

    public function __construct(FileContract $file)
    {
        $this->strict = true;
        $this->file = $file;
        $this->assertFileExists();
        $this->assertFilePhp();
    }

    public function withNoStrict(): FileReturn
    {
        $new = clone $this;
        $new->strict = false;

        return $new;
    }

    public function file(): FileContract
    {
        return $this->file;
    }

    public function checksum(): string
    {
        return hash_file(static::CHECKSUM_ALGO, $this->file->path()->absolute());
    }

    public function contents(): string
    {
        return file_get_contents($this->file->path()->absolute());
    }

    public function return()
    {
        $this->assertFileExists();
        $this->validate();

        return include $this->file->path()->absolute();
    }

    /**
     * Gets the content of the file appling unserialize.
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
     * Put $var into the file using var_export return
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
        $this->file->put(
            FileReturn::PHP_RETURN . $varExport . ';'
        );
    }

    public function destroyCache()
    {
        if (!opcache_invalidate($this->file->path()->absolute())) {
            throw new RuntimeException(
                (new Message('Opcode cache is disabled'))
                    ->toString()
            );
        }
    }

    private function assertFileExists(): void
    {
        if (!$this->file->exists()) {
            throw new FileNotFoundException(
                (new Message('Instance of %className% must represents a file in the path %path%'))
                    ->code('%className%', get_class($this->file))
                    ->code('%path%', $this->file->path())
                    ->toString()

            );
        }
    }

    private function assertFilePhp(): void
    {
        if (!$this->file->isPhp()) {
            throw new FileNotPhpException(
                (new Message('Instance of %className% must represents a PHP script in the path %path%'))
                    ->code('%className%', get_class($this->file))
                    ->code('%path%', $this->file->path())
                    ->toString()
            );
        }
        return;
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
        $handle = fopen($this->file->path()->absolute(), 'r');
        if (false === $handle) {
            throw new RuntimeException(
                (new Message('Unable to %fn% %path% in %mode% mode'))
                    ->code('%fn%', 'fopen')
                    ->code('%path%', $this->file->path()->absolute())
                    ->code('%mode%', 'r')
                    ->toString()
            );
        }
        $contents = fread($handle, static::PHP_RETURN_CHARS);
        fclose($handle);
        if ($contents !== static::PHP_RETURN) {
            throw new RuntimeException(
                (new Message('Unexpected contents in %path% (strict validation)'))
                    ->code('%path%', $this->file->path()->absolute())
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
                    ->code('%path%', $this->file->path()->absolute())
                    ->toString()
            );
        }
        if (!preg_match_all('#<\?php([\S\s]*)\s*return\s*[\S\s]*;#', $contents)) {
            throw new RuntimeException(
                (new Message('Unexpected contents in %path% (non-strict validation)'))
                    ->code('%path%', $this->file->path()->absolute())
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
