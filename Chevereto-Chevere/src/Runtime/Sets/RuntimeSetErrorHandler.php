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

namespace Chevere\Runtime\Sets;

use Chevere\Contracts\Runtime\RuntimeSetContract;
use Chevere\Runtime\Traits\RuntimeSet;

class RuntimeSetErrorHandler implements RuntimeSetContract
{
    use RuntimeSet;

    const ID = 'errorHandler';

    public function set(): void
    {
        if (null == $this->value) {
            $this->restoreErrorHandler();
        } else {
            set_error_handler($this->value);
        }
    }

    private function restoreErrorHandler(): void
    {
        restore_error_handler();
        $this->value = set_error_handler(function () { });
        restore_error_handler();
    }
}
