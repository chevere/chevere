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
 * Describes the component in charge of defining a parameter of type float.
 */
interface FloatParameterInterface extends ParameterInterface
{
    public const MIN = -PHP_FLOAT_MIN;

    public const MAX = PHP_FLOAT_MAX;

    /**
     * Asserts the given `$value` is valid, returning the value if so.
     */
    public function __invoke(float $value): float;

    /**
     * Return an instance with the specified `$default` value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$default` value.
     */
    public function withDefault(float $value): self;

    /**
     * Return an instance with the specified minimum value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified minimum value.
     */
    public function withMin(float $value): self;

    /**
     * Return an instance with the specified maximum value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified maximum value.
     */
    public function withMax(float $value): self;

    /**
     * Return an instance with the specified accepted value(s).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified accepted value(s).
     *
     * When using this method it will nullify the minimum and maximum values.
     */
    public function withAccept(float ...$value): self;

    /**
     * Provides access to the default value (if any).
     */
    public function default(): ?float;

    /**
     * Provides access to the minimum value.
     */
    public function min(): ?float;

    /**
     * Provides access to the maximum value.
     */
    public function max(): ?float;

    /**
     * Provides access to the accepted value(s).
     *
     * @return float[]
     */
    public function accept(): array;

    public function assertCompatible(self $parameter): void;
}
