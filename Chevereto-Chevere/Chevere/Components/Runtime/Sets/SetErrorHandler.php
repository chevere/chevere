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
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Contracts\Sets\SetErrorHandlerContract;
use Chevere\Components\Runtime\Exceptions\InvalidArgumentException;
use Chevere\Components\Runtime\Traits\HandlerTrait;

/**
 * Sets the exception handler using `set_error_handler`
 */
class SetErrorHandler implements SetErrorHandlerContract
{
    use SetTrait;
    use HandlerTrait;

    /** @var mixed Value returned from PHP */
    private $handler;

    /**
     * Sets the error handler function
     *
     * @param string $value A full-qualified callable name or empty string for restore handler.
     * @throws InvalidArgumentException If the value passed isn't acceptable.
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        if ('' == $this->value) {
            $this->restoreErrorHandler();

            return;
        }
        $this->assertArgument();
        set_error_handler($this->value);
    }

    private function assertArgument(): void
    {
        if (!is_callable($this->value)) {
            throw new InvalidArgumentException(
                (new Message('Value must be a valid %type% for %subject%, value %value% passed'))
                    ->code('%type%', 'callable')
                    ->code('%subject%', 'set_error_handler()')
                    ->code('%value%', $this->value)
                    ->toString()
            );
        }
    }

    private function restoreErrorHandler(): void
    {
        restore_error_handler();
        $this->handler = set_error_handler(function () {});
        try {
            $this->value = $this->handler ?? '';
        } catch (TypeError $e) {
            $this->value = '@';
        }
        restore_error_handler();
    }
}
