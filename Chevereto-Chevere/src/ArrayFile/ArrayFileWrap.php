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

namespace Chevere\ArrayFile;

use ArrayIterator;
use IteratorAggregate;

/**
 * Provides wrapping for ArrayFile.
 */
final class ArrayFileWrap implements IteratorAggregate
{
    /** @var ArrayFile */
    private $arrayFile;

    /** @var array */
    private $array;

    public function __construct(ArrayFile $arrayFile, callable $callback)
    {
        foreach ($arrayFile as $k => $v) {
            $callback($k, $v);
        }
        $this->arrayFile = $arrayFile;
        $this->array = $this->arrayFile->toArray();
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->array);
    }

    public function arrayFile(): ArrayFile
    {
        return $this->arrayFile;
    }
}
