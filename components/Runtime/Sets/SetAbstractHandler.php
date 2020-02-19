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

namespace Chevere\Components\Runtime\Sets;

use InvalidArgumentException;
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Interfaces\SetInterface;
use Error;
use Throwable;

/**
 * Sets the abstract handler using `getSetHandler`
 */
abstract class SetAbstractHandler implements SetInterface
{
    use SetTrait;

    protected $handler;

    /**
     * Sets the handler callable.
     * @param string $value A callable name.
     *
     * @throws InvalidArgumentException If $value is not callable
     */
    final public function __construct(string $value)
    {
        $this->value = $value;
        if ('' == $this->value) {
            $this->restoreHandler();

            return;
        }
        $this->assertArgument();
        $this->handler = $this->value;
        $this->getSetHandler()($this->value);
    }

    abstract public function getSetHandler(): callable;

    abstract public function getRestoreHandler(): callable;

    public function handler()
    {
        return $this->handler;
    }

    private function assertArgument(): void
    {
        if (!is_callable($this->value)) {
            throw new InvalidArgumentException(
                (new Message('Runtime value must be a valid callable for %subject%'))
                    ->code('%subject%', strval($this->getSetHandler()))
                    ->toString()
            );
        }
    }

    private function restoreHandler(): void
    {
        $restoreFn = $this->getRestoreHandler();
        $setFn = $this->getSetHandler();
        $restoreFn();
        $this->handler = $setFn(fn () => '');
        try {
            $this->value = (string) $this->handler;
        } catch (Throwable $e) {
            $this->value = '@';
        }
        $restoreFn();
    }
}
