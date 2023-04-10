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
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Chevere\Throwable\Exceptions\OverflowException;
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
     * Return an instance with the specified required parameter(s) added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified required parameter(s) added.
     *
     * @throws OverflowException
     */
    public function withAddedRequired(ParameterInterface ...$parameter): self;

    /**
     * Return an instance with the specified optional parameter(s) added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional parameter(s) added.
     *
     * @throws OverflowException
     */
    public function withAddedOptional(ParameterInterface ...$parameter): self;

    /**
     * Return an instance with the specified parameter(s) removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified parameter(s) removed.
     */
    public function withOut(string ...$name): self;

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

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): ParameterInterface;

    /**
     * @return array<string>
     */
    public function required(): array;

    /**
     * @return array<string>
     */
    public function optional(): array;
}
