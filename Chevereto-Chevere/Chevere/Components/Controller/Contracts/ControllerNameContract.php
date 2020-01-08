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

namespace Chevere\Components\Controller\Contracts;

use Chevere\Components\Common\Contracts\ToStringContract;

interface ControllerNameContract extends ToStringContract
{
    /**
     * Creates a new instance.
     */
    public function __construct(string $name);

    /**
     * @return string Controller name.
     */
    public function toString(): string;
}
