<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Data\Traits;

use Chevere\Components\Data\Contracts\DataContract;

trait DataMethodTrait
{
    use DataPropertyTrait;

    /**
     * Provides access to the DataContract instance.
     */
    public function data(): DataContract
    {
        return $this->data;
    }
}
