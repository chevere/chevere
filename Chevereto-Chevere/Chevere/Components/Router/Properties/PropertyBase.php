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

namespace Chevere\Components\Router\Properties;

use Throwable;
use TypeError;
use Chevere\Components\Breadcrum\Breadcrum;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Breadcrum\Contracts\BreadcrumContract;

abstract class PropertyBase
{
    protected BreadcrumContract $breadcrum;

    protected function assertString($var): void
    {
        if (!is_string($var)) {
            throw new TypeError(
                (new Message('Expecting type %expected%, type %provided% provided'))
                    ->code('%expected%', 'string')
                    ->code('%provided%', gettype($var))
                    ->toString()
            );
        }
    }

    protected function assertInt($var): void
    {
        if (!is_int($var)) {
            throw new TypeError(
                (new Message('Expecting type %expected%, type %provided% provided'))
                    ->code('%expected%', 'int')
                    ->code('%provided%', gettype($var))
                    ->toString()
            );
        }
    }

    protected function assertStringNotEmpty(string $var): void
    {
        if ('' == $var) {
            throw new TypeError(
                (new Message('Empty string provided'))
                    ->toString()
            );
        }
    }

    protected function assertArrayNotEmpty(array $var): void
    {
        if (empty($var)) {
            throw new TypeError(
                (new Message('Empty array provided'))
                    ->toString()
            );
        }
    }

    /**
     * This method asserts the property and catch-throw any exception.
     */
    protected function tryAsserts(): void
    {
        $this->breadcrum = new Breadcrum();
        try {
            $this->asserts();
        } catch (Throwable $e) {
            throw new RouterPropertyException(
                $this->getMessage($e)
                    ->toString()
            );
        }
    }

    protected function getMessage(Throwable $e): Message
    {
        $message = new Message($e->getMessage());
        if ($this->breadcrum->hasAny()) {
            $message = (new Message('%exception% at %at%'))
                ->translate('%exception%', $e->getMessage())
                ->code('%at%', $this->breadcrum->toString());
        }

        return $message;
    }

    /**
     * Returns a Message with the following placeholders:
     * - %for%
     * - %expected%
     * - %provided%.
     *
     * Placeholders can be recplaced using Message::code, Message::strtr, etc.
     */
    protected function getBadTypeMessage(): Message
    {
        return new Message('Type for %for% must be type %expected%, type %provided% provided');
    }

    /**
     * Assert the property.
     *
     * @throws Throwable if anything goes wrong
     */
    abstract protected function asserts(): void;
}
