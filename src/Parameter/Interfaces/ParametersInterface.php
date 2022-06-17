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
 */
interface ParametersInterface extends MappedInterface
{
    public function __construct(ParameterInterface ...$parameters);

    /**
     * @return Iterator<string, ParameterInterface>
     */
    public function getIterator(): Iterator;

    /**
     * Return an instance with the specified required `$parameters` instance added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified required `$parameters` instance added.
     *
     * @throws OverflowException
     */
    public function withAdded(ParameterInterface ...$parameters): self;

    /**
     * Return an instance with the specified optional `$parameters` instance added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional `$parameters` instance added.
     *
     * @throws OverflowException
     */
    public function withAddedOptional(ParameterInterface ...$parameters): self;

    /**
     * Return an instance with the specified `$parameters` modifying an already added parameter.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$parameters` modifying an already added parameter.
     *
     * @throws OutOfBoundsException
     */
    public function withModify(ParameterInterface ...$parameters): self;

    /**
     * Asserts whether the instance has a parameter by name(s).
     */
    public function assertHas(string ...$parameter): void;

    /**
     * Indicates whether the instance has a parameter by name(s).
     */
    public function has(string ...$parameter): bool;

    /**
     * Indicates whether the `$parameter` identified by its name is required.
     *
     * @throws OutOfBoundsException
     */
    public function isRequired(string $parameter): bool;

    /**
     * Indicates whether the `$parameter` identified by its name is optional.
     *
     * @throws OutOfBoundsException
     */
    public function isOptional(string $parameter): bool;

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $parameter): ParameterInterface;

    /**
     * @return array<string>
     */
    public function required(): array;

    /**
     * @return array<string>
     */
    public function optional(): array;
}
