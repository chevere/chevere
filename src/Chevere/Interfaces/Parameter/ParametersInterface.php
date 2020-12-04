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
use Chevere\Interfaces\DataStructures\MappedInterface;
use Generator;

/**
 * Describes the component in charge of collecting objects implementing `ParameterInterface`.
 */
interface ParametersInterface extends MappedInterface
{
    /**
     * @return Generator<string, ParameterInterface>
     */
    public function getGenerator(): Generator;

    /**
     * Return an instance with the specified required `$parameter` instance added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified required `$parameter` instance added.
     *
     * @throws OverflowException
     */
    public function withAddedRequired(ParameterInterface ...$parameter): ParametersInterface;

    /**
     * Return an instance with the specified optional `$parameter` instance added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional `$parameter` instance added.
     *
     * @throws OverflowException
     */
    public function withAddedOptional(ParameterInterface ...$parameter): ParametersInterface;

    /**
     * Return an instance with the specified `$parameter` modifying an already added parameter.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$parameter` modifying an already added parameter.
     *
     * @throws OutOfBoundsException
     */
    public function withModify(ParameterInterface ...$parameter): ParametersInterface;

    /**
     * Indicates whether the instance has a parameter by name.
     */
    public function has(string $parameter): bool;

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

    public function countRequired(): int;

    public function countOptional(): int;
}
