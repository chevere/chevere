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

/**
 * Describes the component in charge of defining a parameter of type array with string members.
 */
interface ArrayStringParameterInterface extends ArrayTypeParameterInterface
{
    /**
     * Return an instance with the specified default value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified default value.
     *
     * @param array<mixed, string> $value
     */
    public function withDefault(array $value): self;

    /**
     * Return an instance with required parameters.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified required parameters.
     */
    public function withRequired(StringParameterInterface ...$parameter): self;

    /**
     * Return an instance with optional parameters.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional parameters.
     */
    public function withOptional(StringParameterInterface ...$parameter): self;

    /**
     * Return an instance with removed parameters.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added parameters.
     */
    public function without(string ...$name): self;

    public function assertCompatible(self $parameter): void;

    /**
     * Return an instance requiring at least `$count` of optional arguments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified optional parameters.
     */
    public function withOptionalMinimum(int $count): self;
}
