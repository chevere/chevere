<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Contracts\Controller;

use Chevere\Contracts\ToArrayContract;

interface InspectContract extends ToArrayContract
{
    /**
     * @param string $className A class name implementing the ControllerContract
     */
    public function __construct(string $className);
}
