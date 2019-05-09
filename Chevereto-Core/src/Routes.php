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

namespace Chevereto\Core;

/**
 * Routes provides a way to interact with files that return a PHP array.
 */
class Routes
{
    /** @var string */
    protected $fileHandle;

    /** @var array */
    // protected $array;

    /** @var ArrayFile */
    protected $arrayFile;

    public function __construct(string $fileHandle)
    {
        $this->setFileHandle($fileHandle);
    }

    public function getFileHandle(): string
    {
        return $this->fileHandle;
    }

    public function setFileHandle(string $fileHandle): self
    {
        $this->setArrayFile(new ArrayFile($fileHandle, Route::class));
        $this->fileHandle = $fileHandle;

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
