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

use Chevere\Common\Interfaces\ToArrayInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;

/**
 * Describes the component in charge of defining a set of parameters with arguments.
 */
interface ArgumentsInterface extends ParametersAccessInterface, ToArrayInterface
{
    /**
     * Provides access to the arguments as array.
     * @phpstan-ignore-next-line
     */
    public function toArray(): array;

    /**
     * Provides access to the arguments as array, filling non-provided optional arguments.
     * @phpstan-ignore-next-line
     */
    public function toArrayFill(mixed $fill): array;

    /**
     * Return an instance with the specified argument.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified argument.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException If `$name` is not a known parameter.
     */
    public function withPut(string $name, mixed $value): self;

    /**
     * Indicates whether the instance has an argument for the parameter `$name`.
     */
    public function has(string ...$name): bool;

    /**
     * Provides access to the argument value for the parameter `$name`.
     * @return mixed Value OR null (when `$name` is an optional parameter with no value)
     * @throws OutOfBoundsException
     */
    public function get(string $name): mixed;

    /**
     * Provides access to the required argument for the parameter `$name`.
     *
     * @throws OutOfBoundsException
     */
    public function required(string $name): CastArgumentInterface;

    /**
     * Provides access to the optional argument for the parameter `$name`.
     *
     * @throws OutOfBoundsException
     */
    public function optional(string $name): ?CastArgumentInterface;
}
