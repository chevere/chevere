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

namespace Chevere\Components\VarDump\Contracts;

use Chevere\Components\Common\Contracts\ToStringContract;

interface WrapperContract
{
    public function __construct(string $key);

    public function wrap(string $dump): string;
}
