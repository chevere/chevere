<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\FileReturn;

use RuntimeException;
use Chevere\File;
use Chevere\Message;
use Chevere\Path\PathHandle;

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

    /** @var string Absolute path to file */
    private $path;

    /** @var string File checksum */
    private $checksum;

    /** @var string The file contents */
    private $contents;

    /** @var string Raw file return (no serialize) */
    private $raw;

    /** @var mixed A variable (PHP code) */
    private $var;

    /** @var bool True for strick validation (PHP_RETURN), false for regex validation (return <algo>) */
    private $strict;

    public function __construct(PathHandle $pathHandle)
    {
        $this->strict =  true;
        $this->path = $pathHandle->path();
    }

    public function setStrict(bool $toggle)
    {
        $this->strict = $toggle;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function checksum(): string
    {
        if (!isset($this->checksum)) {
            $this->checksum = $this->getHashFile();
        }
        return $this->checksum;
    }

    public function raw()
    {
        if (!isset($this->raw)) {
            if (!File::exists($this->path)) {
                throw new RuntimeException(
                    (new Message("File %filepath% file doesn't exists"))
                        ->code('%filepath%', $this->path)
                        ->toString()
                );
            }
            $this->validate();
            $this->raw = include $this->path;
        }
        return $this->raw;
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
                foreach ($this->var as $k => &$v) {
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
    public function put($var)
    {
        if (is_iterable($var)) {
            foreach ($var as $k => &$v) {
                $this->switchVar($v);
            }
        } else {
            $this->switchVar($var);
        }
        $varExport = var_export($var, true);
        $export = FileReturn::PHP_RETURN . $varExport . ';';
        File::put($this->path, $export);
        $this->checksum = $this->getHashFile();
    }

    /**
     * OPCache the FileReturn file
     */
    public function compile()
    {
        opcache_compile_file($this->path);
    }

    public function invalidateCache()
    {
        opcache_invalidate($this->path);
    }

    private function getHashFile()
    {
        return hash_file(static::CHECKSUM_ALGO, $this->path);
    }

    private function validate()
    {
        if ($this->strict) {
            $this->validateStrict();
        } else {
            $this->validateNonStrict();
        }
    }

    private function validateStrict(): void
    {
        $handle = fopen($this->path, 'r');
        if (!$handle) {
            throw new RuntimeException(
                (new Message('Unable to %fn% %filepath% in %mode% mode'))
                    ->code('%fn%', 'fopen')
                    ->code('%filepath%', $this->path)
                    ->code('%mode%', 'r')
                    ->toString()
            );
        }
        $contents = fread($handle, static::PHP_RETURN_CHARS);
        fclose($handle);
        if ($contents !== static::PHP_RETURN) {
            throw new RuntimeException(
                (new Message('Unexpected contents in %filepath% (strict validation)'))
                    ->code('%filepath%', $this->path)
                    ->toString()
            );
        }
    }

    private function validateNonStrict(): void
    {
        $contents =  file_get_contents($this->path);
        if (!$contents) {
            throw new RuntimeException(
                (new Message('Unable to get file %filepath% contents'))
                    ->code('%filepath%', $this->path)
                    ->toString()
            );
        }
        if (!preg_match_all('#<\?php([\S\s]*)\s*return\s*[\S\s]*;#', $contents)) {
            throw new RuntimeException(
                (new Message('Unexpected contents in %filepath% (non-strict validation)'))
                    ->code('%filepath%', $this->path)
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
    }
}
