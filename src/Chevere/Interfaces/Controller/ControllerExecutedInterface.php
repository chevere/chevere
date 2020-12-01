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

namespace Chevere\Interfaces\Controller;

use Throwable;

/**
 * Describes the component in charge of handling the controller execution outcome.
 */
interface ControllerExecutedInterface
{
    public function __construct(array $data);

    /**
     * Provides access to the controller returned code.
     */
    public function code(): int;

    /**
     * Provides access to the controller returned data.
     */
    public function data(): array;

    /**
     * Return an instance with the specified throwable, with its exit code.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified throwable, with its exit code.
     */
    public function withThrowable(Throwable $throwable, int $code): ControllerExecutedInterface;

    /**
     * Indicates whether the instance has a `\Throwable`.
     */
    public function hasThrowable(): bool;

    /**
     * Provides access to the `\Throwable` instance.
     */
    public function throwable(): Throwable;
}
