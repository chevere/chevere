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

use Chevere\Interfaces\Controller\ControllerParameterInterface;
use Countable;
use Generator;

/**
 * Describes the component in charge of collecting objects implementing `ControllerParameterInterface`.
 */
interface ControllerParametersInterface extends Countable
{
    /**
     * @return Generator<string, ControllerParameterInterface> getGenerator()
     */
    public function getGenerator(): Generator;

    /**
     * Provides access to an array representation.
     *
     * ```php
     * return [
     *     'name' => $controllerParameter,
     * ];
     * ```
     */
    public function toArray(): array;

    /**
     * Return an instance with the specified controller parameter instance.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller parameter instance.
     */
    public function withParameter(ControllerParameterInterface $controllerParameter): ControllerParametersInterface;

    /**
     * Indicates whether the instance has a parameter identified by `$name`.
     */
    public function hasParameterName(string $name): bool;

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): ControllerParameterInterface;
}
