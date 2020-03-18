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

namespace Chevere\Components\Spec\Specs;

use Chevere\Components\DataStructures\SplObjectStorageRead;
use Chevere\Components\Spec\GroupSpec;

final class GroupSpecObjectsRead extends SplObjectStorageRead
{
    /**
     * @return GroupSpec
     */
    public function current(): GroupSpec
    {
        return $this->objects->current();
    }

    public function getInfo()
    {
        return $this->objects->getInfo();
    }
}
