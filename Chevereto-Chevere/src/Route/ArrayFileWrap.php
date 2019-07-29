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

namespace Chevere\Route;

use Chevere\PathHandle;
use Chevere\ArrayFile;

/**
 * Provides a way to interact with Route array files.
 */
final class ArrayFileWrap
{
    /** @var ArrayFile */
    private $arrayFile;

    public function __construct(PathHandle $pathHandle)
    {
        $this->setArrayFile(new ArrayFile($pathHandle, Route::class));
    }

    public function getArrayFile(): ArrayFile
    {
        return $this->arrayFile;
    }

    private function setArrayFile(ArrayFile $arrayFile)
    {
        foreach ($arrayFile->toArray() as $k => $route) {
            $route->setId((string) $k);
        }
        $this->arrayFile = $arrayFile;

        return $this;
    }
}
