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
 * Describes the component in charge of defining a parameter of type bool.
 */
interface BoolParameterInterface extends ParameterInterface
{
    /**
     * Asserts the given `$value` is valid, returning the value if so.
     */
    public function __invoke(bool $value): bool;

    /**
     * Return an instance with the specified `$default` value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$default` value.
     */
    public function withDefault(bool $value): self;

    /**
     * Provides access to the default value (if any).
     */
    public function default(): ?bool;

    public function assertCompatible(self $parameter): void;
}
