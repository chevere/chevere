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

namespace Chevereto\Chevere\Route;

use Chevereto\Chevere\PathHandle;
use Chevereto\Chevere\ArrayFile;

/**
 * Routes provides a way to interact with Route array files.
 */
class ArrayFileWrap
{
    /** @var string */
    protected $fileHandle;

    /** @var array */
    // protected $array;

    /** @var ArrayFile */
    protected $arrayFile;

    public function __construct(PathHandle $pathHandle)
    {
        $this->setFileHandle($pathHandle);
    }

    public function getFileHandle(): string
    {
        return $this->fileHandle;
    }

    public function setFileHandle(PathHandle $pathHandle): self
    {
        $this->setArrayFile(new ArrayFile($pathHandle, Route::class));
        $this->fileHandle = $pathHandle->getPath();

        return $this;
    }

    public function getArrayFile(): ArrayFile
    {
        return $this->arrayFile;
    }

    protected function setArrayFile(ArrayFile $arrayFile)
    {
        foreach ($arrayFile->toArray() as $k => $route) {
            $route->setId((string) $k);
        }
        $this->arrayFile = $arrayFile;

        return $this;
    }
}
