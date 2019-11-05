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

namespace Chevere\Components\FileReturn;

use RuntimeException;

use Chevere\Components\FileReturn\Exceptions\FileNotFoundException;
use Chevere\Components\File\File;
use Chevere\Components\Message\Message;

/**
 * FileReturn provides an abstraction for interacting with PHP files that return a variable.
 *
 * <?php return 'Hello World!';
 *
 * Which is used like this:
 *
 * $var = include 'file.php';
 *
 * This class provides extra object support using serialization.
 *
 */
final class FileReturn
{
    const PHP_RETURN = "<?php\n\nreturn ";
    const PHP_RETURN_CHARS = 14;
    const CHECKSUM_ALGO = 'sha256';

    /** @var File */
    private $file;

    /** @var string File checksum */
    private $checksum;

    /** @var string The file contents */
    private $contents;

    /** @var mixed Raw return statement var */
    private $raw;

    /** @var string The raw return type (gettype) */
    private $type;

    /** @var mixed A variable (PHP code) */
    private $var;

    /** @var bool True for strict validation (PHP_RETURN), false for regex validation (return <algo>) */
    private $strict;

    public function __construct(File $file)
    {
        $this->strict = true;
        $this->file = $file;
    }

    public function withNoStrict(): FileReturn
    {
        $new = clone $this;
        $new->strict = false;

        return $new;
    }

    public function file(): File
    {
        return $this->file;
    }

    public function checksum(): string
    {
        if (!isset($this->checksum)) {
            $this->checksum = $this->getHashFile();
        }
        return $this->checksum;
    }

    public function contents(): string
    {
        if (!isset($this->contents)) {
            $this->contents = file_get_contents($this->file->path()->absolute());
        }
        return $this->contents;
    }

    public function raw()
    {
        if (!isset($this->raw)) {
            if (!$this->file->exists()) {
                throw new FileNotFoundException(
                    (new Message("File %filepath% doesn't exists."))
                        ->code('%filepath%', $this->file->path()->absolute())
                        ->toString()
                );
            }
            $this->validate();
            $this->raw = include $this->file->path()->absolute();
            $this->type = gettype($this->raw);
        }
        return $this->raw;
    }

    public function type(): string
    {
        if (!isset($this->type)) {
            $this->type = gettype($this->raw());
        }
        return $this->type;
    }

    /**
     * Gets the content of the file appling unserialize.
     * TODO: Rename to something with more context
     */
    public function get()
    {
        if (!isset($this->var)) {
            $this->var = $this->raw();
            if (is_iterable($this->var)) {
                foreach ($this->var as &$v) {
                    $this->unseralize($v);
                }
            } else {
                $this->unseralize($this->var);
            }
        }
        return $this->var;
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
        $export = FileReturn::PHP_RETURN . $varExport . ';';
        $this->file->put($export);
        $this->checksum = $this->getHashFile();
        unset($this->contents);
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

    private function getHashFile()
    {
        return hash_file(static::CHECKSUM_ALGO, $this->file->path()->absolute());
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
        $this->contents = $this->contents();
        if (!$this->contents) {
            throw new RuntimeException(
                (new Message('Unable to get file %path% contents'))
                    ->code('%path%', $this->file->path()->absolute())
                    ->toString()
            );
        }
        if (!preg_match_all('#<\?php([\S\s]*)\s*return\s*[\S\s]*;#', $this->contents)) {
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

    private function unseralize(&$var)
    {
        $var = unserialize($var);
        dd($var);
    }
}
