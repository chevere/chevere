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
use Chevere\Interfaces\Permission\ConditionInterface;
use Chevere\Interfaces\Permission\ConditionsInterface;

final class Conditions implements ConditionsInterface
{
    use CollectionTrait;

    public function withAdded(ConditionInterface $condition): ConditionsInterface
    {
        return $this->withAssertAdd($condition);
    }

    public function withModify(ConditionInterface $condition): ConditionsInterface
    {
        return $this->withAssertModify($condition);
    }

    public function get(string $name): ConditionInterface
    {
        return $this->assertGet($name);
    }
}
