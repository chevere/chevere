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

class RuntimeSetExceptionHandler implements RuntimeSetContract
{
    use RuntimeSet;

    const ID = 'exceptionHandler';

    /** @var string */
    private $value;

    public function set(): void
    {
        if (null == $this->value) {
            $this->restoreExceptionHandler();
        } else {
            set_exception_handler($this->value);
        }
    }

    private function restoreExceptionHandler(): void
    {
        restore_exception_handler();
        $this->value = set_exception_handler(function () { });
        restore_exception_handler();
    }
}
