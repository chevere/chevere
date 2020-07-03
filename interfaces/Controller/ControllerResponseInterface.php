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

/**
 * Describes the component in charge of handling the controller response.
 */
interface ControllerResponseInterface
{
    public function __construct(bool $isSuccess, array $data);

    /**
     * Indicates whether the instance represents a success response.
     */
    public function isSuccess(): bool;

    /**
     * Provides access to controller response data.
     */
    public function data(): array;

    /**
     * Return an instance with the specified success flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified success flag.
     */
    public function withIsSuccess(bool $isSuccess): ControllerResponseInterface;

    /**
     * Return an instance with the specified data.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified data.
     */
    public function withData(array $data): ControllerResponseInterface;
}
