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

namespace Chevere\Tests\DataStructure\src;

use Chevere\DataStructure\Interfaces\VectorInterface;
use Chevere\DataStructure\Traits\VectorTrait;

final class UsesVectorTrait implements VectorInterface
{
    use VectorTrait;
}