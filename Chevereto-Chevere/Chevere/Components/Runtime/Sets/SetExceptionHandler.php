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

use TypeError;
use InvalidArgumentException;
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Interfaces\SetInterface;
use Chevere\Components\Runtime\Traits\HandlerTrait;

/**
 * Sets the exception handler using `set_exception_handler`
 */
class SetExceptionHandler implements SetInterface
{
    use SetTrait;
    use HandlerTrait;

    /**
     * Sets the exception handler function
     *
     * @param string $value A full-qualified callable name or empty string for restore handler.
     * @throws InvalidArgumentException If the value passed isn't acceptable.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        if ('' == $this->value) {
            $this->restoreExceptionHandler();

            return;
        }
        $this->assertArgument();
        set_exception_handler($this->value);
    }

    private function assertArgument(): void
    {
        if (!is_callable($this->value)) {
            throw new InvalidArgumentException(
                (new Message('Runtime value must be a valid callable for %subject%'))
                    ->code('%subject%', 'set_exception_handler')
                    ->toString()
            );
        }
    }

    private function restoreExceptionHandler(): void
    {
        restore_exception_handler();
        $this->handler = set_exception_handler(function () {
        });
        try {
            $this->value = $this->handler ?? '';
        } catch (TypeError $e) {
            $this->value = '@';
        }
        restore_exception_handler();
    }
}
