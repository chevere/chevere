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

namespace Chevere\Parameter\Interfaces;

use Chevere\DataStructure\Interfaces\MappedInterface;
use Chevere\DataStructure\Interfaces\VectorInterface;
use Iterator;

/**
 * Describes the component in charge of collecting objects implementing `ParameterInterface`.
 *
 * @extends MappedInterface<ParameterInterface>
 */
interface ParametersInterface extends MappedInterface
{
    /**
     * @return Iterator<string, ParameterInterface>
     */
    public function getIterator(): Iterator;

    /**
     * Return an instance with the specified required parameter added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified required parameter added.
     */
    public function withRequired(string $name, ParameterInterface $parameter): self;

    /**
     * Return an instance with the specified optional parameter(s) added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional parameter(s) added.
     */
    public function withOptional(string $name, ParameterInterface $parameter): self;

    /**
     * Return an instance with the specified now optional parameter(s).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified now optional parameter(s).
     */
    public function withMakeOptional(string ...$name): self;

    /**
     * Return an instance with the specified now required parameter(s).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified now required parameter(s).
     */
    public function withMakeRequired(string ...$name): self;

    /**
     * Return an instance with the specified parameter(s) removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified parameter(s) removed.
     */
    public function without(string ...$name): self;

    /**
     * Return an instance requiring at least `$count` of optional arguments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional parameters.
     */
    public function withOptionalMinimum(int $count): self;

    /**
     * Asserts whether the instance has a parameter by name(s).
     */
    public function assertHas(string ...$name): void;

    /**
     * Indicates whether the instance has a parameter by name(s).
     */
    public function has(string ...$name): bool;

    /**
     * Indicates whether the parameter(s) identified by its name is required.
     */
    public function isRequired(string ...$name): bool;

    /**
     * Indicates whether the parameter(s) identified by its name is optional.
     */
    public function isOptional(string ...$name): bool;

    public function requiredKeys(): VectorInterface;

    public function optionalKeys(): VectorInterface;

    public function optionalMinimum(): int;

    public function get(string $key): ParameterInterface;

    public function required(string $key): CastParameterInterface;

    public function optional(string $key): CastParameterInterface;
}
