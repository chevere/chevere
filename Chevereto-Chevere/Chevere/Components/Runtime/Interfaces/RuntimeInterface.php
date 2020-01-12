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

namespace Chevere\Components\Runtime\Interfaces;

use Chevere\Components\Data\Interfaces\DataContract;
use Chevere\Components\Data\Interfaces\DataMethodContract;

interface RuntimeInterface extends DataMethodContract
{
    public function __construct(SetInterface ...$setContract);

    /**
     * Provides access to the DataContract instance.
     *
     * @return DataContract A data contract with set names as keys.
     */
    public function data(): DataContract;
}
