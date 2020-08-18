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
use Chevere\Interfaces\Permission\EnumInterface;
use Chevere\Interfaces\Permission\EnumsInterface;

final class Enums implements EnumsInterface
{
    use CollectionTrait;

    public function withAdded(EnumInterface $enum): EnumsInterface
    {
        return $this->withAssertAdd($enum);
    }

    public function withModify(EnumInterface $enum): EnumsInterface
    {
        return $this->withAssertModify($enum);
    }

    public function get(string $name): EnumInterface
    {
        return $this->assertGet($name);
    }
}
