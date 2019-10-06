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
use Chevere\Runtime\Traits\RuntimeSet;
use Chevere\Contracts\Runtime\SetContract;

class SetExceptionHandler implements SetContract
{
    use RuntimeSet;

    public function set(): void
    {
        if (null == $this->value) {
            $this->restoreExceptionHandler();
        } else {
            if (!is_callable($this->value)) {
                throw new InvalidArgumentException(
                    (new Message('Runtime value must be a valid callable for %subject%'))
                        ->code('%subject%', 'set_exception_handler')
                );
            }
            set_exception_handler($this->value);
        }
    }

    private function restoreExceptionHandler(): void
    {
        restore_exception_handler();
        $this->value = (string) set_exception_handler(function () { });
        restore_exception_handler();
    }
}
