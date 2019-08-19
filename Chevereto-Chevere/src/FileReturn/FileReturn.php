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
use InvalidArgumentException;

final class FileReturn
{
    const PHP_RETURN = "<?php\n\nreturn ";
    const PHP_RETURN_CHARS = 14;
    
    /** @var string */
    private $path;

    private $raw;

    public function __construct(PathHandle $pathHandle)
    {
        $this->path = $pathHandle->path();
        if (!File::exists($this->path)) {
            throw new RuntimeException(
                (new Message("File %filepath% file doesn't exists"))
                    ->code('%filepath%', $this->path)
                    ->toString()
            );
        }
        $this->validate();
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
            $this->raw = include $this->path;
        }
        return $this->raw;
    }

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

    private function getHashFile()
    {
        return hash_file(static::CHECKSUM_ALGO, $this->path);
    }

    private function validate()
    {
        $handle = fopen($this->path, 'r');
        $contents = fread($handle, static::PHP_RETURN_CHARS);
        fclose($handle);
        if ($contents !== static::PHP_RETURN) {
            throw new RuntimeException(
                (new Message('Unexpected contents in %filepath%'))
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
