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
use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use Chevere\Interfaces\Description\DescriptionInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Ds\Set;

/**
 * Describes the component in charge of defining a parameter.
 */
interface ParameterInterface extends DescriptionInterface
{
    /**
     * Provides access to the type instance.
     */
    public function type(): TypeInterface;

    /**W
     * Return an instance with the specified `$description`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$description`.
     */
    public function withDescription(string $description): ParameterInterface;

    /**
     * Return an instance with the specified `$attribute` added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$attribute` added.
     *
     * @throws OverflowException
     */
    public function withAddedAttribute(string $attribute): ParameterInterface;

    /**
     * Return an instance with the specified `$attribute` removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$attribute` removed.
     *
     * @throws OutOfBoundsException
     */
    public function withRemovedAttribute(string $attribute): ParameterInterface;

    /**
     * Indicates whether the instance has the given `$attribute`.
     */
    public function hasAttribute(string $attribute): bool;

    /**
     * Provides access to the attributes instance.
     */
    public function attributes(): Set;
}
