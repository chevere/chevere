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

use Chevere\File;
use Chevere\Path\PathHandle;

final class Maker
{
    const CHECKSUM_ALGO = 'sha512';
    
    /** @var string */
    private $path;

    private $var;

    /** @var string */
    private $checksum;

    public function __construct(PathHandle $pathHandle, $var)
    {
        $this->path = $pathHandle->path();
        $this->var = $var;
        $this->put();
    }

    public function path(): string
    {
        return $this->path;
    }

    public function checksum(): string
    {
        return $this->checksum;
    }

    private function put()
    {
        if (is_iterable($this->var)) {
            foreach ($this->var as $k => &$v) {
                $this->switchVar($v);
            }
        } else {
            $this->switchVar($this->var);
        }
        $varExport = var_export($this->var, true);
        $export = FileReturn::PHP_RETURN . $varExport . ';';
        File::put($this->path, $export);
        $this->checksum = hash_file(static::CHECKSUM_ALGO, $this->path);
    }

    private function switchVar(&$var)
    {
        if (is_object($var)) {
            $var = serialize($var);
        }
    }
}
