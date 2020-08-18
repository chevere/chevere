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

namespace Chevere\Components\Permission;

use Chevere\Components\Permission\Traits\CollectionTrait;
use Chevere\Interfaces\Permission\RangeInterface;
use Chevere\Interfaces\Permission\RangesInterface;

final class Ranges implements RangesInterface
{
    use CollectionTrait;

    public function withAdded(RangeInterface $range): RangesInterface
    {
        return $this->withAssertAdd($range);
    }

    public function withModify(RangeInterface $range): RangesInterface
    {
        return $this->withAssertModify($range);
    }

    public function get(string $name): RangeInterface
    {
        return $this->assertGet($name);
    }
}
