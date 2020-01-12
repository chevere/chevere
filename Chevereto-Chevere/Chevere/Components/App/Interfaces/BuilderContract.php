<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App\Interfaces;

interface BuilderContract
{
    public function __construct(BuildInterface $build);

    /**
     * Return an instance with the specified BuildContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified BuildContract.
     */
    public function withBuild(BuildInterface $build): BuilderContract;

    /**
     * Provides access to the BuildContract instance.
     */
    public function build(): BuildInterface;

    /**
     * Return an instance with the specified controller.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller.
     */
    public function withControllerName(string $controller): BuilderContract;

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
    public function withControllerArguments(array $arguments): BuilderContract;

    /**
     * Returns a boolean indicating whether the instance has controller arguments.
     */
    public function hasControllerArguments(): bool;

    /**
     * Provides access to the controller arguments.
     */
    public function controllerArguments(): array;
}
