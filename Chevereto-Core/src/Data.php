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

use Chevereto\Core\Interfaces\DataInterface;
use Chevereto\Core\Traits\DataTrait;
use IteratorAggregate;
use Countable;
use ArrayIterator;

class Data implements DataInterface, IteratorAggregate, Countable
{
    use DataTrait;

    public function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->data = $data;
        }
    }

    /**
     * Returns an iterator for data.
     *
     * @return ArrayIterator An ArrayIterator instance
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Returns the number of keys.
     *
     * @return int The number of keys
     */
    public function count()
    {
        return count($this->data);
    }
}
