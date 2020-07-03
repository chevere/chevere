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

/**
 * Describes the component in charge of handling controller instructions.
 */
interface ControllerInterface
{
    /**
     * Defines the controller parameters.
     */
    public function getParameters(): ControllerParametersInterface;

    /**
     * Provides access to the controller parameters defined with `getParameters()`.
     */
    public function parameters(): ControllerParametersInterface;

    /**
     * Defines the controller description.
     */
    public function getDescription(): string;

    /**
     * Provides access to the description defined with `getDescription()`.
     */
    public function description(): string;

    /**
     * This method will be called before running `run()`.
     */
    public function setUp(): void;

    /**
     * This method will be called after running `run()`.
     */
    public function tearDown(): void;

    /**
     * This method will be called when running the controller.
     */
    public function run(ControllerArgumentsInterface $controllerArguments): ControllerResponseInterface;
}
