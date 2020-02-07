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

namespace Chevere\Components\Data\Traits;

use Chevere\Components\Data\Interfaces\DataInterface;

trait DataMethodTrait
{
    use DataPropertyTrait;

    /**
     * Provides access to the DataInterface instance.
     */
    public function data(): DataInterface
    {
        return $this->data;
    }
}
