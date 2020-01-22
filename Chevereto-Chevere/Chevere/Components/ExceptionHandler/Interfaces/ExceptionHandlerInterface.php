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

namespace Chevere\Components\ExceptionHandler\Interfaces;

use DateTimeInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;

interface ExceptionHandlerInterface
{
    /**
     * Provides access to the instance DateTime UTC.
     */
    public function dateTimeUtc(): DateTimeInterface;

    /**
     * Provides access to the instance exception.
     */
    public function exception(): ExceptionInterface;

    /**
     * Provides access to the instance id.
     */
    public function id(): string;

    /**
     * Return an instance with the specified debug flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified debug flag.
     */
    public function withIsDebug(bool $isDebug): ExceptionHandlerInterface;

    /**
     * Provides access to the instance debug flag.
     */
    public function isDebug(): bool;

    /**
     * Return an instance with the specified RequestInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RequestInterface.
     */
    public function withRequest(RequestInterface $request): ExceptionHandlerInterface;

    /**
     * Returns a boolean indicating whether the instance has a RequestInterface.
     */
    public function hasRequest(): bool;

    /**
     * Provides access to the RequestInterface instance.
     */
    public function request(): RequestInterface;

    /**
     * Return an instance with the specified log destination.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified log destination.
     */
    public function withLogDestination(string $logDestination): ExceptionHandlerInterface;

    /**
     * Provides access to the instance log destination.
     */
    public function logDestination(): string;
}
