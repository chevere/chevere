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

namespace Chevere\Components\Runtime\Contracts\Sets;

use Chevere\Components\Runtime\Contracts\SetContract;

interface SetErrorHandlerContract extends SetContract
{
    /**
     * @return mixed The handler value as returned by set_error_handler()
     */
    public function handler();
}
