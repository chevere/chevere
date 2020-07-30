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
     * Provides access to an array representation.
     *
     * ```php
     * return [
     *     'name' => $controllerParameter,
     * ];
     * ```
     */
    public function toArray(): array;

    /**
     * Return an instance with the specified controller parameter instance.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller parameter instance.
     */
    public function withAdded(ParameterInterface $controllerParameter): ParametersInterface;

    /**
     * Indicates whether the instance has a parameter identified by `$name`.
     */
    public function hasParameterName(string $name): bool;

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): ParameterInterface;
}
