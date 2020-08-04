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

use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Response\ResponseInterface;

/**
 * Describes the component in charge of handling controller instructions.
 */
interface ControllerInterface
{
    /**
     * Defines the controller parameters.
     */
    public function getParameters(): ParametersInterface;

    /**
     * Provides access to the controller parameters defined with `getParameters()`.
     */
    public function parameters(): ParametersInterface;

    /**
     * Defines the controller description.
     */
    public function getDescription(): string;

    /**
     * Provides access to the description defined with `getDescription()`.
     */
    public function description(): string;

    /**
     * Returns a new instance with setup made. Useful to wrap pluggable instructions on parameters and description.
     */
    public function setUp(): ControllerInterface;

    /**
     * Method called when running the controller. This method MUST not alter the state of the instance.
     */
    public function run(ArgumentsInterface $controllerArguments): ResponseInterface;
}
