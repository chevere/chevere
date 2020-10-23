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

use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use Chevere\Interfaces\Description\DescriptionInterface;
use Chevere\Interfaces\Regex\RegexInterface;
use Ds\Set;
use Generator;

/**
 * Describes the component in charge of defining a parameter.
 */
interface ParameterInterface extends DescriptionInterface
{
    /**
     * @throws ParameterNameInvalidException
     */
    public function __construct(string $name);

    /**
     * Provides access to the name.
     */
    public function name(): string;

    /**
     * Provides access to the regex instance.
     */
    public function regex(): RegexInterface;

    /**
     * Return an instance with the specified `$regex`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$regex`.
     */
    public function withRegex(RegexInterface $regex): ParameterInterface;

    /**
     * Return an instance with the specified `$description`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$description`.
     */
    public function withDescription(string $description): ParameterInterface;

    /**
     * Return an instance with the specified `$attribute`.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$attribute`.
     */
    public function withAddedAttribute(string $attribute): ParameterInterface;

    /**
     * Return an instance with the specified `$attribute` removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$attribute` removed.
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
