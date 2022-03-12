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
use TypeError;

/**
 * Describes the component in charge of defining a set of parameters with arguments.
 */
interface ArgumentsInterface extends ToArrayInterface
{
    /**
     * @throws OutOfBoundsException
     * @throws InvalidArgumentException
     */
    public function __construct(ParametersInterface $parameters, mixed ...$namedArguments);

    /**
     * Provides access to the parameters instance.
     */
    public function parameters(): ParametersInterface;

    /**
     * Provides access to the controller arguments as array.
     *
     * ```php
     * return [
     *     'parameterName' => 'argument',
     * ];
     * ```
     */
    public function toArray(): array;

    /**
     * Return an instance with the specified controller argument.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller argument.
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException If `$name` is not a known controller parameter.
     */
    public function withArgument(mixed ...$nameValue): self;

    /**
     * Indicates whether the instance has an argument for the parameter `$name`.
     */
    public function has(string $name): bool;

    /**
     * Provides access to the argument value for the parameter `$name`.
     *
     * @throws OutOfBoundsException
     */
    public function get(string $name): mixed;

    /**
     * Provides access to the argument value for the parameter `$boolean` type-hinted as boolean.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getBoolean(string $name): bool;

    /**
     * Provides access to the argument value for the parameter `$string` type-hinted as string.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getString(string $name): string;

    /**
     * Provides access to the argument value for the parameter `$integer` type-hinted as integer.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getInteger(string $name): int;

    /**
     * Provides access to the argument value for the parameter `$float` type-hinted as float.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getFloat(string $name): float;

    /**
     * Provides access to the argument value for the parameter `$array` type-hinted as array.
     *
     * @return array<mixed, mixed>
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getArray(string $name): array;
}
