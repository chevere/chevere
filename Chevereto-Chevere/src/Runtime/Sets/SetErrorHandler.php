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
use Chevere\Message\Message;
use Chevere\Contracts\Runtime\SetContract;
use Chevere\Runtime\Traits\Set;

class SetErrorHandler implements SetContract
{
    use Set;

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
        $this->value = (string) set_error_handler(function () { });
        restore_error_handler();
    }
}
