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

namespace Chevere\Interfaces\Parameter;

use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Countable;
use Generator;

/**
 * Describes the component in charge of collecting objects implementing `ParameterInterface`.
 */
interface ParametersInterface extends Countable
{
    /**
     * @return Generator<string, ParameterInterface>
     */
    public function getGenerator(): Generator;

    /**
     * Provides access to the array representation of this instance.
     *
     * ```php
     * return [
     *     'name' => $parameter,
     * ];
     * ```
     */
    public function toArray(): array;

    /**
     * Return an instance with the specified `$parameter` instance added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$parameter` instance added.
     *
     * @throws OverflowException
     */
    public function withAdded(ParameterInterface $parameter): ParametersInterface;

    /**
     * Return an instance with the specified `$parameter` modifying an already added parameter.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$parameter` modifying an already added parameter.
     *
     * @throws OutOfBoundsException
     */
    public function withModify(ParameterInterface $parameter): ParametersInterface;

    /**
     * Indicates whether the instance has a parameter by name `$parameter`.
     */
    public function has(string $parameter): bool;

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $parameter): ParameterInterface;
}
