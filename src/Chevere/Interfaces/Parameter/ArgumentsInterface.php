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
use Chevere\Exceptions\Parameter\ArgumentRegexMatchException;
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use Chevere\Interfaces\To\ToArrayInterface;
use TypeError;

/**
 * Describes the component in charge of defining a set of parameters with arguments.
 */
interface ArgumentsInterface extends ToArrayInterface
{
    /**
     * @throws ArgumentRequiredException
     * @throws OutOfBoundsException
     * @throws ArgumentRegexMatchException
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
     * @throws ArgumentRegexMatchException
     * @throws OutOfBoundsException If `$name` is not a known controller parameter.
     */
    public function withArgument(string $name, $value): self;

    /**
     * Indicates whether the instance has an argument for the parameter `$name`.
     */
    public function has(string $name): bool;

    /**
     * Provides access to the argument value for the parameter `$name`.
     *
     * @throws OutOfBoundsException
     */
    public function get(string $name);

    /**
     * Provides access to the argument value for the parameter `$boolean` type-hinted as boolean.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getBoolean(string $boolean): bool;

    /**
     * Provides access to the argument value for the parameter `$string` type-hinted as string.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getString(string $string): string;

    /**
     * Provides access to the argument value for the parameter `$integer` type-hinted as integer.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getInteger(string $integer): int;

    /**
     * Provides access to the argument value for the parameter `$float` type-hinted as float.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getFloat(string $float): float;

    /**
     * Provides access to the argument value for the parameter `$array` type-hinted as array.
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     */
    public function getArray(string $array): array;
}
