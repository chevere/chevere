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
use Chevere\Interfaces\Common\AttributesInterface;
use Chevere\Interfaces\Common\DescriptionInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Ds\Set;

/**
 * Describes the component in charge of defining a parameter.
 */
interface ParameterInterface extends DescriptionInterface, AttributesInterface
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
    public function withDescription(string $description): static;

    /**
     * Return an instance with the specified `$attributes` added.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$attributes` added.
     *
     * @throws OverflowException
     */
    public function withAddedAttribute(string ...$attributes): static;

    /**
     * Return an instance with the specified `$attributes` removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$attributes` removed.
     *
     * @throws OutOfBoundsException
     */
    public function withoutAttribute(string ...$attributes): static;

    /**
     * Indicates whether the instance has the given `$attributes`.
     */
    public function hasAttribute(string ...$attributes): bool;

    /**
     * Provides access to the attributes instance.
     */
    public function attributes(): Set;
}
