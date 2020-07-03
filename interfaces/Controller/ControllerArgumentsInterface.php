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

namespace Chevere\Interfaces\Controller;

use Chevere\Exceptions\Controller\ControllerArgumentRegexMatchException;
use Chevere\Exceptions\Controller\ControllerArgumentRequiredException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Chevere\Interfaces\To\ToArrayInterface;

/**
 * Describes the component in charge of handling controller arguments.
 */
interface ControllerArgumentsInterface extends ToArrayInterface
{
    /**
     * @throws ControllerArgumentRequiredException
     * @throws OutOfBoundsException
     * @throws ControllerArgumentRegexMatchException
     */
    public function __construct(ControllerParametersInterface $parameters, array $arguments);

    /**
     * Provides access to the controller arguments as array.
     *
     * ```php
     * return [
     *     'parameter_name' => 'argument',
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
     * @throws ControllerArgumentRegexMatchException
     * @throws OutOfBoundsException If `$name` is not a known controller parameter.
     */
    public function withArgument(string $name, string $value): ControllerArgumentsInterface;

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
}
