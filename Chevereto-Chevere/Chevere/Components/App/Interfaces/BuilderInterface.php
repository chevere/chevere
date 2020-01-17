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

namespace Chevere\Components\App\Interfaces;

interface BuilderInterface
{
    public function __construct(BuildInterface $build);

    /**
     * Return an instance with the specified BuildInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified BuildInterface.
     */
    public function withBuild(BuildInterface $build): BuilderInterface;

    /**
     * Provides access to the BuildInterface instance.
     */
    public function build(): BuildInterface;

    /**
     * Return an instance with the specified controller.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller.
     */
    public function withControllerName(string $controller): BuilderInterface;

    /**
     * Returns a boolean indicating whether the instance has a controller name.
     */
    public function hasControllerName(): bool;

    /**
     * Provides access to the controller name.
     */
    public function controllerName(): string;

    /**
     * Return an instance with the specified controller arguments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller arguments.
     *
     * @param array $arguments mapping [name => value]
     */
    public function withControllerArguments(array $arguments): BuilderInterface;

    /**
     * Returns a boolean indicating whether the instance has controller arguments.
     */
    public function hasControllerArguments(): bool;

    /**
     * Provides access to the controller arguments.
     */
    public function controllerArguments(): array;
}
