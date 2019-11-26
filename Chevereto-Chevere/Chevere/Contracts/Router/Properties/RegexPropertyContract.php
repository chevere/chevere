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

namespace Chevere\Contracts\Router\Properties;

use Chevere\Contracts\Router\RouterPropertyContract;
use Chevere\Contracts\ToStringContract;

interface RegexPropertyContract extends RouterPropertyContract, ToStringContract
{
    /**
     * Creates a new instance.
     */
    public function __construct(string $regex);
}
