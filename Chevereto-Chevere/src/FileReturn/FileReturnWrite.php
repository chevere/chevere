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
use Chevere\Path\PathHandle;

final class FileReturnWrite
{
    /** @var string */
    private $path;

    public function __construct(PathHandle $pathHandle)
    {
        $this->path = $pathHandle->path();
    }

    public function set($var)
    {
        $this->var = $var;
        if (is_iterable($this->var)) {
            foreach ($this->var as $k => &$v) {
                $this->switchVar($v);
            }
        } else {
            $this->switchVar($this->var);
        }
        $this->varExport = var_export($this->var, true);
        $this->export = FileReturnRead::PHP_RETURN . $this->varExport . ';';
        File::put($this->path, $this->export);
    }

    private function switchVar(&$var)
    {
        if (is_object($var)) {
            $var = serialize($var);
        }
    }
}
