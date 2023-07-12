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
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
use Iterator;
use TypeError;

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
     *
     * @throws OverflowException
     */
    public function withRequired(string $name, ParameterInterface $parameter): self;

    /**
     * Return an instance with the specified optional parameter(s) added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional parameter(s) added.
     *
     * @throws OverflowException
     */
    public function withOptional(string $name, ParameterInterface $parameter): self;

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
    public function withMinimumOptional(int $count): self;

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
     *
     * @throws OutOfBoundsException
     */
    public function isRequired(string ...$name): bool;

    /**
     * Indicates whether the parameter(s) identified by its name is optional.
     *
     * @throws OutOfBoundsException
     */
    public function isOptional(string ...$name): bool;

    public function required(): VectorInterface;

    public function optional(): VectorInterface;

    public function minimumOptional(): int;

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): ParameterInterface;

    /**
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getArray(string $name): ArrayParameterInterface;

    /**
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getBoolean(string $name): BooleanParameterInterface;

    /**
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getFile(string $name): FileParameterInterface;

    /**
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getFloat(string $name): FloatParameterInterface;

    /**
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getInteger(string $name): IntegerParameterInterface;

    /**
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getObject(string $name): ObjectParameterInterface;

    /**
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getString(string $name): StringParameterInterface;
}
