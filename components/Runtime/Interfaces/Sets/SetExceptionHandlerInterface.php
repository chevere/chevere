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

namespace Chevere\Components\Runtime\Interfaces\Sets;

use Chevere\Components\Runtime\Interfaces\SetInterface;

interface SetExceptionHandlerInterface extends SetInterface
{
    /**
     * @return mixed The handler value as returned by `set_exception_handler`
     */
    public function handler();
}
