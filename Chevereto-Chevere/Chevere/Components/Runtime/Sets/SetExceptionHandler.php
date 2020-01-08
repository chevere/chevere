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

namespace Chevere\Components\Runtime\Sets;

use InvalidArgumentException;

use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\Set;
use Chevere\Components\Runtime\Contracts\SetContract;

class SetExceptionHandler implements SetContract
{
    use Set;

    public function set(): void
    {
        if ('' == $this->value) {
            $this->restoreExceptionHandler();
            return;
        }
        if (!is_callable($this->value)) {
            throw new InvalidArgumentException(
                (new Message('Runtime value must be a valid callable for %subject%'))
                    ->code('%subject%', 'set_exception_handler')
                    ->toString()
            );
        }
        set_exception_handler($this->value);
    }

    private function restoreExceptionHandler(): void
    {
        restore_exception_handler();
        $this->value = (string) set_exception_handler(function () {
        });
        restore_exception_handler();
    }
}
