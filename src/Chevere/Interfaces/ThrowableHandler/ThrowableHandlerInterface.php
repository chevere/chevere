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

namespace Chevere\Interfaces\ThrowableHandler;

use Chevere\Exceptions\Core\RuntimeException;
use DateTimeInterface;

/**
 * Describes the component in charge of handling throwables.
 */
interface ThrowableHandlerInterface
{
    /**
     * @throws RuntimeException
     */
    public function __construct(ThrowableReadInterface $throwableRead);

    /**
     * Return an instance with the specified `$debug` flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$debug` flag.
     */
    public function withIsDebug(bool $isDebug): self;

    /**
     * Indicates whether the instance has `debug=true`.
     */
    public function isDebug(): bool;

    /**
     * Provides access to the date time UTC.
     */
    public function dateTimeUtc(): DateTimeInterface;

    /**
     * Provides access to the `$throwableRead` instance.
     */
    public function throwableRead(): ThrowableReadInterface;

    /**
     * Provides access to the handler unique id.
     */
    public function id(): string;
}
