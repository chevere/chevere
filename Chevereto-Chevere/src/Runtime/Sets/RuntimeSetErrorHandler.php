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

use InvalidArgumentException;
use Chevere\Message;
use Chevere\Contracts\Runtime\RuntimeSetContract;
use Chevere\Runtime\Traits\RuntimeSet;

class RuntimeSetErrorHandler implements RuntimeSetContract
{
    use RuntimeSet;

    public function set(): void
    {
        if (null == $this->value) {
            $this->restoreErrorHandler();
        } else {
            if (!is_callable($this->value)) {
                throw new InvalidArgumentException(
                    (new Message('Runtime value must be a valid callable for %subject%'))
                        ->code('%subject%', 'set_error_handler')
                );
            }
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
