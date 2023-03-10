<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\DataStructure\_resources;

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Traits\VectorToArrayTrait;
use Chevere\DataStructure\Vector;

final class UsesVectorArrayTrait
{
    use VectorToArrayTrait;

    public function __construct()
    {
        $this->vector = new Vector('test');
    }

    public function vector(): VectorInterface
    {
        return $this->vector;
    }
}
