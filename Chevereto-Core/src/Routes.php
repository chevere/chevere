<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

/**
 * Routes contains a collection (ArrayFile) of routes defined in a single file.
 */
class Routes
{
    /** @var string */
    protected $fileHandle;
    /** @var array */
    protected $array;

    public function __construct(string $fileHandle)
    {
        $this->setArray((new ArrayFile($fileHandle, Route::class))->toArray());
        $this->setFileHandle($fileHandle);
    }

    public function getArray(): array
    {
        return $this->array ?? [];
    }

    protected function setArray(array $array): self
    {
        $this->array = $array;

        return $this;
    }

    public function getFileHandle(): string
    {
        return $this->fileHandle;
    }

    protected function setFileHandle(string $fileHandle): self
    {
        $this->fileHandle = $fileHandle;

        return $this;
    }
}
